<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MutasiKendaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiMutasiKendaraanController extends Controller
{
    public function update(Request $request, MutasiKendaraan $mutasiKendaraan): RedirectResponse
    {
        if ($mutasiKendaraan->status !== MutasiKendaraan::STATUS_MENUNGGU) {
            return back()->with('error', 'Pengajuan mutasi ini sudah diverifikasi.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:disetujui,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['status'] === MutasiKendaraan::STATUS_DITOLAK && blank($data['catatan_admin'])) {
            return back()->withErrors(['catatan_admin' => 'Catatan admin wajib diisi untuk penolakan mutasi.']);
        }

        DB::transaction(function () use ($request, $mutasiKendaraan, $data): void {
            $mutasiKendaraan->load('kendaraan');

            if ($data['status'] === MutasiKendaraan::STATUS_DISETUJUI
                && (int) $mutasiKendaraan->kendaraan->opd_id !== (int) $mutasiKendaraan->opd_asal_id) {
                abort(422, 'OPD pemegang kendaraan sudah berubah. Mutasi ini tidak dapat disetujui.');
            }

            $mutasiKendaraan->update([
                'status' => $data['status'],
                'catatan_admin' => $data['catatan_admin'],
                'verified_at' => now(),
                'verified_by' => $request->user()->id,
            ]);

            if ($data['status'] === MutasiKendaraan::STATUS_DISETUJUI) {
                $mutasiKendaraan->kendaraan->update([
                    'opd_id' => $mutasiKendaraan->opd_tujuan_id,
                ]);
            }
        });

        return redirect()
            ->route('admin.verifikasi.index', ['jenis' => 'mutasi'])
            ->with('success', 'Status verifikasi mutasi kendaraan berhasil diperbarui.');
    }
}
