<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\MutasiKendaraan;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MutasiKendaraanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $mutasis = MutasiKendaraan::query()
            ->with(['kendaraan', 'opdAsal', 'opdTujuan'])
            ->where('opd_asal_id', $request->user()->opd_id)
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->whereHas('kendaraan', function ($kendaraan) use ($search): void {
                        $kendaraan->where('plat_nomor', 'like', "%{$search}%")
                            ->orWhere('merk', 'like', "%{$search}%")
                            ->orWhere('tipe', 'like', "%{$search}%")
                            ->orWhere('nomor_rangka', 'like', "%{$search}%")
                            ->orWhere('nomor_mesin', 'like', "%{$search}%");
                    })->orWhereHas('opdTujuan', fn ($opd) => $opd->where('nama', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mutasi-kendaraans.index', compact('mutasis', 'search'));
    }

    public function create(Request $request): View
    {
        $opdId = $request->user()->opd_id;

        $kendaraans = Kendaraan::query()
            ->with('opd')
            ->where('opd_id', $opdId)
            ->where('status_verifikasi', Kendaraan::STATUS_DISETUJUI)
            ->whereDoesntHave('mutasiKendaraans', fn ($mutasi) => $mutasi->where('status', MutasiKendaraan::STATUS_MENUNGGU))
            ->orderBy('plat_nomor')
            ->get();

        $opds = Opd::aktif()
            ->whereKeyNot($opdId)
            ->orderBy('nama')
            ->get();

        return view('mutasi-kendaraans.create', compact('kendaraans', 'opds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kendaraan_id' => ['required', 'exists:kendaraans,id'],
            'opd_tujuan_id' => ['required', 'exists:opds,id'],
            'nomor_bast' => ['nullable', 'string', 'max:100'],
            'tanggal_bast' => ['nullable', 'date'],
            'file_bast' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $kendaraan = Kendaraan::findOrFail($data['kendaraan_id']);

        if ($kendaraan->opd_id !== $request->user()->opd_id || $kendaraan->status_verifikasi !== Kendaraan::STATUS_DISETUJUI) {
            abort(403, 'Mutasi hanya dapat diajukan untuk kendaraan terverifikasi milik OPD Anda.');
        }

        if ((int) $data['opd_tujuan_id'] === (int) $request->user()->opd_id) {
            return back()->withErrors(['opd_tujuan_id' => 'OPD tujuan harus berbeda dari OPD asal.'])->withInput();
        }

        $hasPendingMutation = $kendaraan->mutasiKendaraans()
            ->where('status', MutasiKendaraan::STATUS_MENUNGGU)
            ->exists();

        if ($hasPendingMutation) {
            return back()->withErrors(['kendaraan_id' => 'Kendaraan ini masih memiliki pengajuan mutasi yang menunggu verifikasi.'])->withInput();
        }

        $data['opd_asal_id'] = $request->user()->opd_id;
        $data['requested_by'] = $request->user()->id;
        $data['status'] = MutasiKendaraan::STATUS_MENUNGGU;
        $data['submitted_at'] = now();
        $data['file_bast'] = $request->file('file_bast')->storeAs(
            'bast-mutasi',
            $this->uploadFilename($kendaraan->plat_nomor, 'bast', $request->file('file_bast')),
            'public'
        );

        MutasiKendaraan::create($data);

        return redirect()->route('mutasi-kendaraans.index')->with('success', 'Pengajuan mutasi kendaraan berhasil dikirim untuk verifikasi admin.');
    }

    public function destroy(Request $request, MutasiKendaraan $mutasiKendaraan): RedirectResponse
    {
        if ($mutasiKendaraan->opd_asal_id !== $request->user()->opd_id || $mutasiKendaraan->status !== MutasiKendaraan::STATUS_MENUNGGU) {
            abort(403, 'Pengajuan mutasi ini tidak dapat dibatalkan.');
        }

        Storage::disk('public')->delete($mutasiKendaraan->file_bast);
        $mutasiKendaraan->delete();

        return redirect()->route('mutasi-kendaraans.index')->with('success', 'Pengajuan mutasi kendaraan berhasil dibatalkan.');
    }

    private function uploadFilename(?string $platNomor, string $jenis, $file): string
    {
        $plat = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $platNomor)) ?: 'NOPLAT';
        $kode = now()->format('YmdHis').Str::upper(Str::random(6));

        return $plat.$jenis.'-'.$kode.'.'.$file->getClientOriginalExtension();
    }
}
