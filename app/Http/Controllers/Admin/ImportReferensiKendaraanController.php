<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferensiKendaraan;
use App\Support\SimpleSpreadsheetReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImportReferensiKendaraanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $totalReferensi = ReferensiKendaraan::count();
        $sudahDipakai = ReferensiKendaraan::whereHas('kendaraan')->count();
        $referensis = ReferensiKendaraan::query()
            ->withCount('kendaraan')
            ->when($search, fn ($query) => $query->search($search))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.import-referensi.index', compact('totalReferensi', 'sudahDipakai', 'referensis', 'search'));
    }

    public function template()
    {
        $rows = [
            ['Plat Nomor', 'Merk', 'Tipe', 'Tahun', 'Nomor Rangka', 'Nomor Mesin', 'Nomor BPKB'],
            ['G 7 F', 'TOYOTA', 'KIJANG INNOVA 2.0 V A.T', '2017', 'MHXXXXXXXXXXXXXXX', '1TRXXXXXXX', 'BPKB123456'],
        ];

        $content = "\xEF\xBB\xBFsep=;\r\n".collect($rows)
            ->map(fn (array $row): string => collect($row)->map(fn ($value): string => '"'.str_replace('"', '""', (string) $value).'"')->implode(';'))
            ->implode("\r\n");

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="template-import-database-kendaraan.csv"');
    }

    public function store(Request $request, SimpleSpreadsheetReader $reader): RedirectResponse
    {
        $data = $request->validate([
            'file_import' => ['required', 'file', 'extensions:xlsx,csv', 'max:10240'],
        ]);

        $file = $data['file_import'];
        $rows = $reader->rows($file->getRealPath(), $file->getClientOriginalExtension());
        $imported = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $payload = $this->payload($row);

            if (! $payload['plat_nomor'] && ! $payload['nomor_rangka'] && ! $payload['nomor_mesin'] && ! $payload['nomor_bpkb']) {
                $skipped++;

                continue;
            }

            $referensi = ReferensiKendaraan::updateOrCreate(
                ['import_key' => $this->importKey($payload)],
                $payload
            );

            $referensi->wasRecentlyCreated ? $imported++ : $updated++;
        }

        return back()->with('success', "Import selesai. Baru: {$imported}, diperbarui: {$updated}, dilewati: {$skipped}.");
    }

    public function edit(ReferensiKendaraan $referensiKendaraan): View
    {
        return view('admin.import-referensi.edit', [
            'referensi' => $referensiKendaraan,
        ]);
    }

    public function update(Request $request, ReferensiKendaraan $referensiKendaraan): RedirectResponse
    {
        $payload = $this->payload($request->validate($this->rules()));
        $importKey = $this->importKey($payload);

        $exists = ReferensiKendaraan::query()
            ->where('import_key', $importKey)
            ->whereKeyNot($referensiKendaraan->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['plat_nomor' => 'Data referensi dengan nomor rangka/mesin/BPKB yang sama sudah ada.']);
        }

        $referensiKendaraan->update([
            ...$payload,
            'import_key' => $importKey,
        ]);

        return redirect()->route('admin.import-referensi.index')->with('success', 'Data referensi kendaraan berhasil diperbarui.');
    }

    public function destroy(ReferensiKendaraan $referensiKendaraan): RedirectResponse
    {
        if ($referensiKendaraan->kendaraan()->exists()) {
            return back()->with('error', 'Data referensi yang sudah dipilih OPD tidak dapat dihapus.');
        }

        $referensiKendaraan->delete();

        return back()->with('success', 'Data referensi kendaraan berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function rules(): array
    {
        return [
            'plat_nomor' => ['nullable', 'string', 'max:30'],
            'merk' => ['nullable', 'string', 'max:255'],
            'tipe' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'nomor_rangka' => ['nullable', 'string', 'max:100'],
            'nomor_mesin' => ['nullable', 'string', 'max:100'],
            'nomor_bpkb' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function payload(array $row): array
    {
        return [
            'plat_nomor' => $this->normalizeUpper($row['plat_nomor'] ?? null),
            'merk' => $this->normalizeText($row['merk'] ?? null),
            'tipe' => $this->normalizeText($row['tipe'] ?? null),
            'tahun' => $this->normalizeYear($row['tahun'] ?? null),
            'nomor_rangka' => $this->normalizeUpper($row['nomor_rangka'] ?? null),
            'nomor_mesin' => $this->normalizeUpper($row['nomor_mesin'] ?? null),
            'nomor_bpkb' => $this->normalizeUpper($row['nomor_bpkb'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function importKey(array $payload): string
    {
        if ($payload['nomor_rangka']) {
            return 'rangka:'.$payload['nomor_rangka'];
        }

        if ($payload['nomor_mesin']) {
            return 'mesin:'.$payload['nomor_mesin'];
        }

        if ($payload['nomor_bpkb']) {
            return 'bpkb:'.$payload['nomor_bpkb'];
        }

        return 'plat:'.Str::slug(implode('-', [
            $payload['plat_nomor'],
            $payload['merk'],
            $payload['tipe'],
            $payload['tahun'],
        ]));
    }

    private function normalizeUpper(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : strtoupper($value);
    }

    private function normalizeText(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeYear(mixed $value): ?int
    {
        $year = (int) preg_replace('/[^0-9]/', '', (string) $value);

        return $year >= 1900 ? $year : null;
    }
}
