<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KendaraanExportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $kendaraans = Kendaraan::query()
            ->with(['opd', 'creator', 'verifier', 'riwayatPlatNomors'])
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('plat_nomor', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('tipe', 'like', "%{$search}%")
                        ->orWhere('nomor_rangka', 'like', "%{$search}%")
                        ->orWhere('nomor_mesin', 'like', "%{$search}%")
                        ->orWhere('nomor_bpkb', 'like', "%{$search}%")
                        ->orWhere('pengguna_penanggung_jawab', 'like', "%{$search}%")
                        ->orWhere('nip_pengguna_penanggung_jawab', 'like', "%{$search}%")
                        ->orWhereHas('opd', fn ($opd) => $opd->where('nama', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn ($query) => $query->where('status_verifikasi', $status))
            ->orderBy('opd_id')
            ->orderBy('plat_nomor')
            ->get();

        $filename = 'data-kendaraan-'.now()->format('Ymd-His').'.xls';

        return response()
            ->view('admin.exports.kendaraans', compact('kendaraans', 'search', 'status'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Cache-Control', 'max-age=0');
    }
}
