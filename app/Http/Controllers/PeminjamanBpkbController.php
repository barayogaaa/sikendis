<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\PeminjamanBpkb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeminjamanBpkbController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $peminjamans = PeminjamanBpkb::query()
            ->with(['kendaraan', 'opd'])
            ->where('opd_id', $request->user()->opd_id)
            ->when($search, fn ($query) => $query->search($search))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman-bpkbs.index', [
            'peminjamans' => $peminjamans,
            'search' => $search,
            'status' => $status,
            'statusOptions' => PeminjamanBpkb::STATUS_LABELS,
        ]);
    }

    public function create(Request $request): View
    {
        $kendaraans = Kendaraan::query()
            ->where('opd_id', $request->user()->opd_id)
            ->where('status_verifikasi', Kendaraan::STATUS_DISETUJUI)
            ->whereDoesntHave('peminjamanBpkbs', fn ($peminjaman) => $peminjaman->whereIn('status', PeminjamanBpkb::ACTIVE_STATUSES))
            ->orderBy('plat_nomor')
            ->get();

        return view('peminjaman-bpkbs.create', compact('kendaraans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kendaraan_id' => ['required', 'exists:kendaraans,id'],
            'tanggal_rencana_pinjam' => ['required', 'date'],
            'tanggal_rencana_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_rencana_pinjam'],
            'keperluan' => ['required', 'string', 'max:255'],
            'nama_pengambil' => ['required', 'string', 'max:255'],
            'nip_pengambil' => ['nullable', 'string', 'max:30'],
        ]);

        $kendaraan = Kendaraan::findOrFail($data['kendaraan_id']);

        if ($kendaraan->opd_id !== $request->user()->opd_id || $kendaraan->status_verifikasi !== Kendaraan::STATUS_DISETUJUI) {
            abort(403, 'Peminjaman BPKB hanya dapat diajukan untuk kendaraan terverifikasi milik OPD Anda.');
        }

        $hasActiveLoan = $kendaraan->peminjamanBpkbs()
            ->whereIn('status', PeminjamanBpkb::ACTIVE_STATUSES)
            ->exists();

        if ($hasActiveLoan) {
            return back()->withErrors(['kendaraan_id' => 'Kendaraan ini masih memiliki pengajuan atau peminjaman BPKB aktif.'])->withInput();
        }

        if (! empty($data['nip_pengambil'])) {
            $data['nip_pengambil'] = strtoupper(trim($data['nip_pengambil']));
        }

        $data['opd_id'] = $request->user()->opd_id;
        $data['requested_by'] = $request->user()->id;
        $data['status'] = PeminjamanBpkb::STATUS_DIAJUKAN;
        $data['submitted_at'] = now();

        PeminjamanBpkb::create($data);

        return redirect()->route('peminjaman-bpkbs.index')->with('success', 'Pengajuan peminjaman BPKB berhasil dikirim ke admin.');
    }

    public function destroy(Request $request, PeminjamanBpkb $peminjamanBpkb): RedirectResponse
    {
        if ($peminjamanBpkb->opd_id !== $request->user()->opd_id || $peminjamanBpkb->status !== PeminjamanBpkb::STATUS_DIAJUKAN) {
            abort(403, 'Pengajuan peminjaman ini tidak dapat dibatalkan.');
        }

        $peminjamanBpkb->delete();

        return redirect()->route('peminjaman-bpkbs.index')->with('success', 'Pengajuan peminjaman BPKB berhasil dibatalkan.');
    }
}
