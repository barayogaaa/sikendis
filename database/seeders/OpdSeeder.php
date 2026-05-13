<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opds = [
            ['kode' => 'SETDA', 'nama' => 'Sekretariat Daerah'],
            ['kode' => 'DISHUB', 'nama' => 'Dinas Perhubungan'],
            ['kode' => 'BPKAD', 'nama' => 'Badan Pengelola Keuangan dan Aset Daerah'],
        ];

        foreach ($opds as $opd) {
            Opd::updateOrCreate(['kode' => $opd['kode']], $opd + ['aktif' => true]);
        }
    }
}
