<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\ReferensiKendaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $kendaraans = Kendaraan::query()
            ->with('opd')
            ->visibleFor($user)
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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('kendaraans.index', [
            'kendaraans' => $kendaraans,
            'search' => $search,
            'status' => $status,
            'statusOptions' => Kendaraan::STATUS_LABELS,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            return redirect()->route('kendaraans.index');
        }

        return view('kendaraans.create', ['kendaraan' => new Kendaraan]);
    }

    public function searchReferensi(Request $request)
    {
        if ($request->user()->isAdmin()) {
            abort(403);
        }

        $search = trim($request->string('q')->toString());

        if (mb_strlen($search) < 2) {
            return response()->json([]);
        }

        return ReferensiKendaraan::query()
            ->available()
            ->search($search)
            ->orderBy('plat_nomor')
            ->limit(15)
            ->get()
            ->map(fn (ReferensiKendaraan $referensi): array => [
                'id' => $referensi->id,
                'plat_nomor' => $referensi->plat_nomor,
                'merk' => $referensi->merk,
                'tipe' => $referensi->tipe,
                'tahun' => $referensi->tahun,
                'nomor_rangka' => $referensi->nomor_rangka,
                'nomor_mesin' => $referensi->nomor_mesin,
                'nomor_bpkb' => $referensi->nomor_bpkb,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            abort(403);
        }

        $referensi = $request->filled('referensi_kendaraan_id')
            ? ReferensiKendaraan::available()->findOrFail($request->integer('referensi_kendaraan_id'))
            : null;

        $data = $this->validatedData($request, null, $referensi !== null);

        if ($referensi) {
            $data = array_merge($data, [
                'referensi_kendaraan_id' => $referensi->id,
                'plat_nomor' => $referensi->plat_nomor,
                'merk' => $referensi->merk,
                'tipe' => $referensi->tipe,
                'tahun' => $referensi->tahun,
                'nomor_rangka' => $referensi->nomor_rangka,
                'nomor_mesin' => $referensi->nomor_mesin,
                'nomor_bpkb' => $referensi->nomor_bpkb,
            ]);
        }

        $data['opd_id'] = $request->user()->opd_id;
        $data['created_by'] = $request->user()->id;
        $data['status_verifikasi'] = Kendaraan::STATUS_DRAFT;

        $this->storeUploads($request, $data);

        $kendaraan = Kendaraan::create($data);

        return redirect()->route('kendaraans.show', $kendaraan)->with('success', 'Data kendaraan tersimpan sebagai draft.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Kendaraan $kendaraan): View
    {
        $this->authorizeVisibility($request, $kendaraan);

        $kendaraan->load([
            'opd',
            'creator',
            'verifier',
            'riwayatPlatNomors.creator',
            'mutasiKendaraans.opdAsal',
            'mutasiKendaraans.opdTujuan',
        ]);
        $duplicates = $kendaraan->duplicateCandidates()->get();

        return view('kendaraans.show', compact('kendaraan', 'duplicates'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Kendaraan $kendaraan): View
    {
        $this->authorizeVisibility($request, $kendaraan);

        if (! $kendaraan->canBeEditedBy($request->user())) {
            abort(403, 'Data kendaraan ini tidak dapat diedit.');
        }

        return view('kendaraans.edit', compact('kendaraan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $this->authorizeVisibility($request, $kendaraan);

        if (! $kendaraan->canBeEditedBy($request->user())) {
            abort(403, 'Data kendaraan ini tidak dapat diedit.');
        }

        if ($kendaraan->canOnlyEditPenggunaBy($request->user())) {
            $data = $this->validatedVerifiedUpdateData($request);

            $this->storeUploads($request, $data, $kendaraan);

            $kendaraan->update($data);

            return redirect()->route('kendaraans.show', $kendaraan)->with('success', 'Data STNK dan pengguna kendaraan berhasil diperbarui.');
        }

        $referensi = $kendaraan->referensiKendaraan;
        $data = $this->validatedData($request, $kendaraan, $referensi !== null);

        if ($referensi) {
            $data = array_merge($data, [
                'referensi_kendaraan_id' => $referensi->id,
                'plat_nomor' => $referensi->plat_nomor,
                'merk' => $referensi->merk,
                'tipe' => $referensi->tipe,
                'tahun' => $referensi->tahun,
                'nomor_rangka' => $referensi->nomor_rangka,
                'nomor_mesin' => $referensi->nomor_mesin,
                'nomor_bpkb' => $referensi->nomor_bpkb,
            ]);
        }

        $this->storeUploads($request, $data, $kendaraan);

        if ($request->user()->isUserOpd()
            && in_array($kendaraan->status_verifikasi, [Kendaraan::STATUS_REVISI, Kendaraan::STATUS_DITOLAK], true)) {
            $data['status_verifikasi'] = Kendaraan::STATUS_DRAFT;
            $data['catatan_admin'] = $kendaraan->catatan_admin;
        }

        $kendaraan->update($data);

        return redirect()->route('kendaraans.show', $kendaraan)->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $this->authorizeVisibility($request, $kendaraan);

        if (! $kendaraan->canBeDeletedBy($request->user())) {
            abort(403, 'Kendaraan yang sudah disetujui admin tidak dapat dihapus oleh OPD.');
        }

        foreach (['scan_bpkb', 'scan_stnk', 'foto_kendaraan'] as $field) {
            if ($kendaraan->{$field}) {
                Storage::disk('public')->delete($kendaraan->{$field});
            }
        }

        $kendaraan->delete();

        return redirect()->route('kendaraans.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }

    public function submit(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $this->authorizeVisibility($request, $kendaraan);

        if (! $kendaraan->canBeSubmittedBy($request->user())) {
            abort(403, 'Data tidak dapat disubmit pada status saat ini.');
        }

        $missingScan = ! $kendaraan->scan_bpkb && ! $kendaraan->referensi_kendaraan_id;

        if ($missingScan) {
            return back()->with('error', 'Upload scan BPKB sebelum submit verifikasi.');
        }

        $kendaraan->update([
            'status_verifikasi' => Kendaraan::STATUS_MENUNGGU,
            'submitted_at' => now(),
        ]);

        return redirect()->route('kendaraans.show', $kendaraan)->with('success', 'Data kendaraan dikirim untuk verifikasi admin.');
    }

    private function validatedData(Request $request, ?Kendaraan $kendaraan = null, bool $usingReferensi = false): array
    {
        $scanStnkRule = $usingReferensi && ! $kendaraan?->scan_stnk ? 'required' : 'nullable';
        $fotoKendaraanRule = $usingReferensi && ! $kendaraan?->foto_kendaraan ? 'required' : 'nullable';

        $data = $request->validate([
            'referensi_kendaraan_id' => ['nullable', 'exists:referensi_kendaraans,id'],
            'plat_nomor' => ['nullable', 'string', 'max:30'],
            'merk' => [$usingReferensi ? 'nullable' : 'required', 'string', 'max:255'],
            'tipe' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'nomor_rangka' => ['nullable', 'string', 'max:100'],
            'nomor_mesin' => ['nullable', 'string', 'max:100'],
            'nomor_bpkb' => ['nullable', 'string', 'max:100'],
            'tanggal_stnk' => ['nullable', 'date'],
            'pengguna_penanggung_jawab' => [$usingReferensi ? 'required' : 'nullable', 'string', 'max:255'],
            'nip_pengguna_penanggung_jawab' => [$usingReferensi ? 'required' : 'nullable', 'string', 'max:30'],
            'scan_bpkb' => [$usingReferensi || $kendaraan?->scan_bpkb ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'scan_stnk' => [$scanStnkRule, 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'foto_kendaraan' => [$fotoKendaraanRule, 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        foreach (['plat_nomor', 'nomor_rangka', 'nomor_mesin', 'nomor_bpkb', 'nip_pengguna_penanggung_jawab'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = strtoupper(trim($data[$field]));
            }
        }

        return $data;
    }

    private function validatedVerifiedUpdateData(Request $request): array
    {
        $data = $request->validate([
            'pengguna_penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'nip_pengguna_penanggung_jawab' => ['nullable', 'string', 'max:30'],
            'tanggal_stnk' => ['nullable', 'date'],
            'scan_stnk' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (! empty($data['nip_pengguna_penanggung_jawab'])) {
            $data['nip_pengguna_penanggung_jawab'] = strtoupper(trim($data['nip_pengguna_penanggung_jawab']));
        }

        return $data;
    }

    private function storeUploads(Request $request, array &$data, ?Kendaraan $kendaraan = null): void
    {
        $uploads = [
            'scan_bpkb' => 'scan-bpkb',
            'scan_stnk' => 'scan-stnk',
            'foto_kendaraan' => 'foto-kendaraan',
        ];

        foreach ($uploads as $field => $directory) {
            if (! $request->hasFile($field)) {
                continue;
            }

            if ($kendaraan?->{$field}) {
                Storage::disk('public')->delete($kendaraan->{$field});
            }

            $data[$field] = $request->file($field)->storeAs(
                $directory,
                $this->uploadFilename($data['plat_nomor'] ?? $kendaraan?->plat_nomor, $field, $request->file($field)),
                'public'
            );
        }
    }

    private function uploadFilename(?string $platNomor, string $field, $file): string
    {
        $jenis = [
            'scan_bpkb' => 'bpkb',
            'scan_stnk' => 'stnk',
            'foto_kendaraan' => 'foto',
        ][$field] ?? 'berkas';

        $plat = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $platNomor)) ?: 'NOPLAT';
        $kode = now()->format('YmdHis').Str::upper(Str::random(6));

        return $plat.$jenis.'-'.$kode.'.'.$file->getClientOriginalExtension();
    }

    private function authorizeVisibility(Request $request, Kendaraan $kendaraan): void
    {
        if ($request->user()->isUserOpd() && $request->user()->opd_id !== $kendaraan->opd_id) {
            abort(403, 'OPD hanya dapat mengakses kendaraan milik OPD sendiri.');
        }
    }
}
