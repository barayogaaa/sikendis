<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\MutasiKendaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiKendaraanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $jenis = $request->string('jenis')->toString() ?: 'bpkb';
        $status = $request->string('status')->toString() ?: Kendaraan::STATUS_MENUNGGU;

        if ($jenis === 'mutasi') {
            $status = $request->string('status')->toString() ?: MutasiKendaraan::STATUS_MENUNGGU;

            $mutasis = MutasiKendaraan::query()
                ->with(['kendaraan', 'opdAsal', 'opdTujuan', 'requester'])
                ->when($status !== 'semua', fn ($query) => $query->where('status', $status))
                ->when($search, function ($query) use ($search): void {
                    $query->where(function ($q) use ($search): void {
                        $q->whereHas('kendaraan', function ($kendaraan) use ($search): void {
                            $kendaraan->where('plat_nomor', 'like', "%{$search}%")
                                ->orWhere('merk', 'like', "%{$search}%")
                                ->orWhere('tipe', 'like', "%{$search}%")
                                ->orWhere('nomor_rangka', 'like', "%{$search}%")
                                ->orWhere('nomor_mesin', 'like', "%{$search}%");
                        })
                            ->orWhereHas('opdAsal', fn ($opd) => $opd->where('nama', 'like', "%{$search}%"))
                            ->orWhereHas('opdTujuan', fn ($opd) => $opd->where('nama', 'like', "%{$search}%"));
                    });
                })
                ->latest('submitted_at')
                ->paginate(10)
                ->withQueryString();

            return view('admin.verifikasi.index', [
                'jenis' => $jenis,
                'mutasis' => $mutasis,
                'kendaraans' => null,
                'search' => $search,
                'status' => $status,
                'statusOptions' => ['semua' => 'Semua'] + MutasiKendaraan::STATUS_LABELS,
            ]);
        }

        $kendaraans = Kendaraan::query()
            ->with('opd')
            ->when($status !== 'semua', fn ($query) => $query->where('status_verifikasi', $status))
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('plat_nomor', 'like', "%{$search}%")
                        ->orWhere('nomor_rangka', 'like', "%{$search}%")
                        ->orWhere('nomor_mesin', 'like', "%{$search}%")
                        ->orWhereHas('opd', fn ($opd) => $opd->where('nama', 'like', "%{$search}%"));
                });
            })
            ->latest('submitted_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi.index', [
            'jenis' => $jenis,
            'kendaraans' => $kendaraans,
            'mutasis' => null,
            'search' => $search,
            'status' => $status,
            'statusOptions' => ['semua' => 'Semua'] + Kendaraan::STATUS_LABELS,
        ]);
    }

    public function update(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $data = $request->validate([
            'status_verifikasi' => ['required', 'in:disetujui,revisi,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        if (in_array($data['status_verifikasi'], [Kendaraan::STATUS_REVISI, Kendaraan::STATUS_DITOLAK], true)
            && blank($data['catatan_admin'])) {
            return back()->withErrors(['catatan_admin' => 'Catatan admin wajib diisi untuk revisi atau penolakan.']);
        }

        $kendaraan->update([
            'status_verifikasi' => $data['status_verifikasi'],
            'catatan_admin' => $data['catatan_admin'],
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        return redirect()->route('kendaraans.show', $kendaraan)->with('success', 'Status verifikasi kendaraan berhasil diperbarui.');
    }
}
