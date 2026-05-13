<?php

namespace App\Support;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class SimpleSpreadsheetReader
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function rows(string $path, string $extension): array
    {
        return match (strtolower($extension)) {
            'csv' => $this->csvRows($path),
            'xlsx' => $this->xlsxRows($path),
            default => throw new RuntimeException('Format file import harus .xlsx atau .csv.'),
        };
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function csvRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if (! $handle) {
            throw new RuntimeException('File import tidak dapat dibaca.');
        }

        $rows = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($row) === 1 && str_starts_with(strtolower(trim((string) $row[0])), 'sep=')) {
                continue;
            }

            if (count($row) === 1 && str_contains((string) $row[0], ';')) {
                $row = str_getcsv((string) $row[0], ';');
            } elseif (count($row) === 1 && str_contains((string) $row[0], "\t")) {
                $row = str_getcsv((string) $row[0], "\t");
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $this->rowsWithHeaders($rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function xlsxRows(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            return $this->xlsxRowsFromExtractedPath($path);
        }

        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new RuntimeException('File Excel tidak dapat dibuka.');
        }

        $sharedStrings = $this->sharedStringsFromXml($zip->getFromName('xl/sharedStrings.xml'));
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        if ($sheetXml === false) {
            $zip->close();

            throw new RuntimeException('Sheet pertama tidak ditemukan pada file Excel.');
        }

        $sheet = new SimpleXMLElement($sheetXml);
        $rows = [];

        foreach ($sheet->sheetData->row as $row) {
            $values = [];

            foreach ($row->c as $cell) {
                $attributes = $cell->attributes();
                $column = $this->columnIndex((string) $attributes['r']);
                $type = (string) $attributes['t'];
                $value = '';

                if ($type === 's') {
                    $value = $sharedStrings[(int) $cell->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                } else {
                    $value = (string) ($cell->v ?? '');
                }

                $values[$column] = trim($value);
            }

            if ($values !== []) {
                ksort($values);
                $rows[] = array_values($values);
            }
        }

        $zip->close();

        return $this->rowsWithHeaders($rows);
    }

    /**
     * @return array<int, string>
     */
    private function sharedStrings(ZipArchive $zip): array
    {
        return $this->sharedStringsFromXml($zip->getFromName('xl/sharedStrings.xml'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function xlsxRowsFromExtractedPath(string $path): array
    {
        $directory = storage_path('framework/cache/xlsx-import-'.uniqid());
        mkdir($directory, 0777, true);

        $zipPath = $directory.DIRECTORY_SEPARATOR.'workbook.zip';
        copy($path, $zipPath);

        $command = 'powershell -NoProfile -Command "Expand-Archive -LiteralPath '.escapeshellarg($zipPath).' -DestinationPath '.escapeshellarg($directory).' -Force"';
        shell_exec($command);

        $sheetPath = $directory.DIRECTORY_SEPARATOR.'xl'.DIRECTORY_SEPARATOR.'worksheets'.DIRECTORY_SEPARATOR.'sheet1.xml';

        if (! file_exists($sheetPath)) {
            $this->deleteDirectory($directory);

            throw new RuntimeException('Sheet pertama tidak ditemukan pada file Excel.');
        }

        $sharedStrings = $this->sharedStringsFromXml(
            file_exists($directory.DIRECTORY_SEPARATOR.'xl'.DIRECTORY_SEPARATOR.'sharedStrings.xml')
                ? file_get_contents($directory.DIRECTORY_SEPARATOR.'xl'.DIRECTORY_SEPARATOR.'sharedStrings.xml')
                : false
        );

        $sheet = new SimpleXMLElement((string) file_get_contents($sheetPath));
        $rows = [];

        foreach ($sheet->sheetData->row as $row) {
            $values = [];

            foreach ($row->c as $cell) {
                $attributes = $cell->attributes();
                $column = $this->columnIndex((string) $attributes['r']);
                $type = (string) $attributes['t'];
                $value = '';

                if ($type === 's') {
                    $value = $sharedStrings[(int) $cell->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                } else {
                    $value = (string) ($cell->v ?? '');
                }

                $values[$column] = trim($value);
            }

            if ($values !== []) {
                ksort($values);
                $rows[] = array_values($values);
            }
        }

        $this->deleteDirectory($directory);

        return $this->rowsWithHeaders($rows);
    }

    /**
     * @return array<int, string>
     */
    private function sharedStringsFromXml(string|false $xml): array
    {
        if ($xml === false) {
            return [];
        }

        $strings = [];
        $shared = new SimpleXMLElement($xml);

        foreach ($shared->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;

                continue;
            }

            $text = '';

            foreach ($item->r as $run) {
                $text .= (string) ($run->t ?? '');
            }

            $strings[] = $text;
        }

        return $strings;
    }

    private function deleteDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = array_diff(scandir($directory) ?: [], ['.', '..']);

        foreach ($items as $item) {
            $path = $directory.DIRECTORY_SEPARATOR.$item;

            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }

    private function columnIndex(string $cellReference): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($cellReference));
        $index = 0;

        foreach (str_split((string) $letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(0, $index - 1);
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function rowsWithHeaders(array $rows): array
    {
        $rows = array_values(array_filter($rows, fn (array $row): bool => collect($row)->filter(fn ($value) => filled($value))->isNotEmpty()));

        if ($rows === []) {
            return [];
        }

        $headers = array_map(fn ($header): string => $this->normalizeHeader((string) $header), array_shift($rows));
        $mapped = [];

        foreach ($rows as $row) {
            $item = [];

            foreach ($headers as $index => $header) {
                if ($header !== '') {
                    $item[$header] = trim((string) ($row[$index] ?? ''));
                }
            }

            if (collect($item)->filter(fn ($value) => filled($value))->isNotEmpty()) {
                $mapped[] = $item;
            }
        }

        return $mapped;
    }

    private function normalizeHeader(string $header): string
    {
        $key = strtolower(trim($header));
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);
        $key = trim((string) $key, '_');

        return match ($key) {
            'plat', 'nopol', 'no_polisi', 'nomor_polisi', 'plat_nomor' => 'plat_nomor',
            'merk', 'merek' => 'merk',
            'type', 'tipe' => 'tipe',
            'th', 'tahun' => 'tahun',
            'rangka', 'no_rangka', 'nomor_rangka' => 'nomor_rangka',
            'mesin', 'no_mesin', 'nomor_mesin' => 'nomor_mesin',
            'bpkb', 'no_bpkb', 'nomor_bpkb' => 'nomor_bpkb',
            default => $key,
        };
    }
}
