<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\RiwayatPlatNomor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RiwayatPlatNomorController extends Controller
{
    public function store(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $this->authorizeKendaraanVisibility($request, $kendaraan);

        if (! $kendaraan->canManageRiwayatPlatBy($request->user())) {
            abort(403, 'Riwayat perubahan nopol hanya dapat diisi saat kendaraan masih draft.');
        }

        $data = $this->validatedData($request);
        $data['created_by'] = $request->user()->id;

        $kendaraan->riwayatPlatNomors()->create($data);

        return redirect()
            ->route('kendaraans.show', $kendaraan)
            ->with('success', 'Riwayat perubahan nopol berhasil ditambahkan.');
    }

    public function destroy(Request $request, RiwayatPlatNomor $riwayatPlatNomor): RedirectResponse
    {
        $riwayatPlatNomor->load('kendaraan');
        $kendaraan = $riwayatPlatNomor->kendaraan;

        $this->authorizeKendaraanVisibility($request, $kendaraan);

        if (! $kendaraan->canManageRiwayatPlatBy($request->user())) {
            abort(403, 'Riwayat perubahan nopol hanya dapat dihapus saat kendaraan masih draft.');
        }

        $riwayatPlatNomor->delete();

        return redirect()
            ->route('kendaraans.show', $kendaraan)
            ->with('success', 'Riwayat perubahan nopol berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'plat_nomor_lama' => ['required', 'string', 'max:30'],
            'plat_nomor_baru' => ['required', 'string', 'max:30'],
            'tanggal_perubahan' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach (['plat_nomor_lama', 'plat_nomor_baru'] as $field) {
            $data[$field] = strtoupper(trim($data[$field]));
        }

        return $data;
    }

    private function authorizeKendaraanVisibility(Request $request, Kendaraan $kendaraan): void
    {
        if ($request->user()->isUserOpd() && $request->user()->opd_id !== $kendaraan->opd_id) {
            abort(403, 'OPD hanya dapat mengakses kendaraan milik OPD sendiri.');
        }
    }
}
