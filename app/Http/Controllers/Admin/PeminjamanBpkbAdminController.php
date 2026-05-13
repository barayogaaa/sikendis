<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanBpkb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeminjamanBpkbAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status', '')->toString();

        $peminjamans = PeminjamanBpkb::query()
            ->with(['kendaraan', 'opd'])
            ->when($search, fn ($query) => $query->search($search))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $belumMengembalikan = PeminjamanBpkb::where('status', PeminjamanBpkb::STATUS_DIPINJAM)->count();
        $menungguVerifikasi = PeminjamanBpkb::where('status', PeminjamanBpkb::STATUS_DIAJUKAN)->count();
        $siapDiambil = PeminjamanBpkb::where('status', PeminjamanBpkb::STATUS_DISETUJUI)->count();

        return view('admin.peminjaman-bpkbs.index', [
            'peminjamans' => $peminjamans,
            'search' => $search,
            'status' => $status,
            'statusOptions' => PeminjamanBpkb::STATUS_LABELS,
            'belumMengembalikan' => $belumMengembalikan,
            'menungguVerifikasi' => $menungguVerifikasi,
            'siapDiambil' => $siapDiambil,
        ]);
    }

    public function update(Request $request, PeminjamanBpkb $peminjamanBpkb): RedirectResponse
    {
        $data = $request->validate([
            'aksi' => ['required', 'in:setujui,tolak,pinjamkan,kembalikan'],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['aksi'] === 'tolak' && blank($data['catatan_admin'])) {
            return back()->withErrors(['catatan_admin' => 'Catatan admin wajib diisi saat menolak peminjaman BPKB.']);
        }

        match ($data['aksi']) {
            'setujui' => $this->setujui($request, $peminjamanBpkb, $data['catatan_admin']),
            'tolak' => $this->tolak($request, $peminjamanBpkb, $data['catatan_admin']),
            'pinjamkan' => $this->pinjamkan($request, $peminjamanBpkb, $data['catatan_admin']),
            'kembalikan' => $this->kembalikan($request, $peminjamanBpkb, $data['catatan_admin']),
        };

        return back()->with('success', 'Status peminjaman BPKB berhasil diperbarui.');
    }

    private function setujui(Request $request, PeminjamanBpkb $peminjamanBpkb, ?string $catatan): void
    {
        if ($peminjamanBpkb->status !== PeminjamanBpkb::STATUS_DIAJUKAN) {
            abort(422, 'Hanya pengajuan baru yang dapat disetujui.');
        }

        $peminjamanBpkb->update([
            'status' => PeminjamanBpkb::STATUS_DISETUJUI,
            'catatan_admin' => $catatan,
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);
    }

    private function tolak(Request $request, PeminjamanBpkb $peminjamanBpkb, ?string $catatan): void
    {
        if ($peminjamanBpkb->status !== PeminjamanBpkb::STATUS_DIAJUKAN) {
            abort(422, 'Hanya pengajuan baru yang dapat ditolak.');
        }

        $peminjamanBpkb->update([
            'status' => PeminjamanBpkb::STATUS_DITOLAK,
            'catatan_admin' => $catatan,
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);
    }

    private function pinjamkan(Request $request, PeminjamanBpkb $peminjamanBpkb, ?string $catatan): void
    {
        if ($peminjamanBpkb->status !== PeminjamanBpkb::STATUS_DISETUJUI) {
            abort(422, 'BPKB hanya dapat dipinjamkan setelah pengajuan disetujui.');
        }

        $peminjamanBpkb->update([
            'status' => PeminjamanBpkb::STATUS_DIPINJAM,
            'catatan_admin' => $catatan ?: $peminjamanBpkb->catatan_admin,
            'dipinjamkan_at' => now(),
            'dipinjamkan_by' => $request->user()->id,
        ]);
    }

    private function kembalikan(Request $request, PeminjamanBpkb $peminjamanBpkb, ?string $catatan): void
    {
        if ($peminjamanBpkb->status !== PeminjamanBpkb::STATUS_DIPINJAM) {
            abort(422, 'Hanya BPKB yang sedang dipinjam yang dapat ditandai dikembalikan.');
        }

        $peminjamanBpkb->update([
            'status' => PeminjamanBpkb::STATUS_DIKEMBALIKAN,
            'catatan_admin' => $catatan ?: $peminjamanBpkb->catatan_admin,
            'dikembalikan_at' => now(),
            'dikembalikan_by' => $request->user()->id,
        ]);
    }
}
