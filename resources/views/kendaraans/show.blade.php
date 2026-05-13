<x-layouts.app heading="Detail Kendaraan">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">Plat nomor</p>
            <h2 class="text-2xl font-black text-slate-950">{{ $kendaraan->plat_nomor ?: '-' }}</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            @include('kendaraans.partials.status', ['status' => $kendaraan->status_verifikasi])
            @if ($kendaraan->canBeEditedBy(auth()->user()))
                <a href="{{ route('kendaraans.edit', $kendaraan) }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                    <x-icon name="edit" class="h-4 w-4"/>
                    Edit
                </a>
            @endif
            @if ($kendaraan->canBeSubmittedBy(auth()->user()))
                <form method="POST" action="{{ route('kendaraans.submit', $kendaraan) }}">
                    @csrf
                    <button class="inline-flex h-10 items-center gap-2 rounded-lg bg-sky-600 px-3 text-sm font-bold text-white transition hover:bg-sky-700">
                        <x-icon name="upload" class="h-4 w-4"/>
                        Submit Verifikasi
                    </button>
                </form>
            @endif
            @if ($kendaraan->canBeDeletedBy(auth()->user()))
                <form method="POST" action="{{ route('kendaraans.destroy', $kendaraan) }}" onsubmit="return confirm('Hapus data kendaraan ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex h-10 items-center gap-2 rounded-lg border border-rose-200 bg-white px-3 text-sm font-bold text-rose-700 transition hover:bg-rose-50">
                        <x-icon name="trash" class="h-4 w-4"/>
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($duplicates->isNotEmpty())
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
            <div class="flex gap-3">
                <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0"/>
                <div>
                    <p class="font-bold">Potensi duplikat terdeteksi</p>
                    <p class="mt-1 text-sm">Ada {{ $duplicates->count() }} data lain dengan nomor rangka atau nomor mesin yang sama.</p>
                </div>
            </div>
        </div>
    @endif

    @if (in_array($kendaraan->status_verifikasi, [\App\Models\Kendaraan::STATUS_REVISI, \App\Models\Kendaraan::STATUS_DITOLAK], true) && $kendaraan->catatan_admin)
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-900">
            <div class="flex gap-3">
                <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0"/>
                <div>
                    <p class="font-bold">Catatan admin</p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-6">{{ $kendaraan->catatan_admin }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_420px]">
        <section class="space-y-3">
            <details open class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <summary class="cursor-pointer list-none px-5 py-4 text-base font-bold">Detail Kendaraan</summary>
                <div class="border-t border-slate-200 p-5">
                    <dl class="grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            'Merk' => $kendaraan->merk,
                            'Tipe' => $kendaraan->tipe,
                            'Tahun' => $kendaraan->tahun,
                            'Nomor Rangka' => $kendaraan->nomor_rangka,
                            'Nomor Mesin' => $kendaraan->nomor_mesin,
                            'Nomor BPKB' => $kendaraan->nomor_bpkb,
                            'Tanggal STNK' => $kendaraan->tanggal_stnk?->format('d/m/Y'),
                            'Pengguna / Penanggung Jawab' => $kendaraan->pengguna_penanggung_jawab,
                            'NIP Pengguna / Penanggung Jawab' => $kendaraan->nip_pengguna_penanggung_jawab,
                            'OPD' => $kendaraan->opd?->nama,
                            'Diinput Oleh' => $kendaraan->creator?->name,
                        ] as $label => $value)
                            <div class="rounded-lg bg-slate-50 p-3">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                                <dd class="mt-1 break-words text-sm font-semibold text-slate-950">{{ $value ?: '-' }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if (auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('admin.verifikasi.update', $kendaraan) }}" class="mt-5 rounded-lg border border-slate-200 p-4">
                            @csrf
                            @method('PATCH')
                            <h3 class="text-sm font-bold">Verifikasi Admin</h3>
                            <div class="mt-3 grid gap-3 sm:grid-cols-[220px_1fr]">
                                <select name="status_verifikasi" class="h-11 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                    <option value="disetujui">Setujui</option>
                                    <option value="revisi">Minta Revisi</option>
                                    <option value="ditolak">Tolak</option>
                                </select>
                                <input name="catatan_admin" value="{{ old('catatan_admin', $kendaraan->catatan_admin) }}" placeholder="Catatan admin" class="h-11 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                            </div>
                            <button class="mt-3 inline-flex h-10 items-center gap-2 rounded-lg bg-slate-900 px-3 text-sm font-bold text-white transition hover:bg-slate-700">
                                <x-icon name="check" class="h-4 w-4"/>
                                Simpan Verifikasi
                            </button>
                        </form>
                    @endif
                </div>
            </details>

            <details class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <summary class="cursor-pointer list-none px-5 py-4 text-base font-bold">Riwayat</summary>
                <div class="space-y-5 border-t border-slate-200 p-5">
                    <div>
                        <h3 class="text-sm font-bold text-slate-950">Riwayat Perubahan Nopol</h3>
                        <p class="mt-1 text-sm text-slate-500">Isi hanya jika pada BPKB ada catatan perubahan nomor polisi.</p>
                    </div>

                    @if ($kendaraan->canManageRiwayatPlatBy(auth()->user()))
                        <form method="POST" action="{{ route('kendaraans.riwayat-plat.store', $kendaraan) }}" class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            @csrf
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nopol Lama</label>
                                    <input name="plat_nomor_lama" value="{{ old('plat_nomor_lama') }}" required class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nopol Baru</label>
                                    <input name="plat_nomor_baru" value="{{ old('plat_nomor_baru') }}" required class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Tanggal Perubahan</label>
                                    <input type="date" name="tanggal_perubahan" value="{{ old('tanggal_perubahan') }}" class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Keterangan BPKB</label>
                                    <input name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: catatan halaman perubahan data BPKB" class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                </div>
                            </div>
                            <button class="mt-4 inline-flex h-10 items-center gap-2 rounded-lg bg-sky-600 px-3 text-sm font-bold text-white transition hover:bg-sky-700">
                                <x-icon name="plus" class="h-4 w-4"/>
                                Tambah Riwayat Nopol
                            </button>
                        </form>
                    @endif

                    <div class="overflow-hidden rounded-lg border border-slate-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Nopol Lama</th>
                                        <th class="px-4 py-3">Nopol Baru</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Keterangan</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($kendaraan->riwayatPlatNomors as $riwayatPlat)
                                        <tr>
                                            <td class="px-4 py-3 font-bold text-slate-950">{{ $riwayatPlat->plat_nomor_lama }}</td>
                                            <td class="px-4 py-3 font-bold text-slate-950">{{ $riwayatPlat->plat_nomor_baru }}</td>
                                            <td class="px-4 py-3 text-slate-600">{{ $riwayatPlat->tanggal_perubahan?->format('d/m/Y') ?: '-' }}</td>
                                            <td class="px-4 py-3 text-slate-600">{{ $riwayatPlat->keterangan ?: '-' }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex justify-end">
                                                    @if ($kendaraan->canManageRiwayatPlatBy(auth()->user()))
                                                        <form method="POST" action="{{ route('kendaraans.riwayat-plat.destroy', $riwayatPlat) }}" onsubmit="return confirm('Hapus riwayat perubahan nopol ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700" title="Hapus riwayat">
                                                                <x-icon name="trash" class="h-4 w-4"/>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-slate-400">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat perubahan nopol.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-950">Riwayat Mutasi OPD</h3>
                        <div class="mt-3 overflow-hidden rounded-lg border border-slate-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 text-sm">
                                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3">OPD Asal</th>
                                            <th class="px-4 py-3">OPD Tujuan</th>
                                            <th class="px-4 py-3">BAST</th>
                                            <th class="px-4 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @forelse ($kendaraan->mutasiKendaraans as $mutasi)
                                            <tr>
                                                <td class="px-4 py-3">{{ $mutasi->opdAsal?->nama }}</td>
                                                <td class="px-4 py-3">{{ $mutasi->opdTujuan?->nama }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="block font-semibold">{{ $mutasi->nomor_bast ?: '-' }}</span>
                                                    <span class="block text-xs text-slate-500">{{ $mutasi->tanggal_bast?->format('d/m/Y') ?: '-' }}</span>
                                                </td>
                                                <td class="px-4 py-3">@include('mutasi-kendaraans.partials.status', ['status' => $mutasi->status])</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat mutasi OPD.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            <details class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <summary class="cursor-pointer list-none px-5 py-4 text-base font-bold">QR Code</summary>
                <div class="border-t border-slate-200 p-5">
                    <div class="grid h-40 place-items-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-sm font-semibold text-slate-500">
                        Area QR Code kendaraan untuk tahap berikutnya.
                    </div>
                </div>
            </details>
        </section>

        <aside class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-base font-bold">Preview Scan BPKB</h3>
                <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                    @if ($kendaraan->scan_bpkb)
                        @php
                            $url = asset('storage/'.$kendaraan->scan_bpkb);
                            $isPdf = str_ends_with(strtolower($kendaraan->scan_bpkb), '.pdf');
                        @endphp
                        @if ($isPdf)
                            <iframe src="{{ $url }}" class="h-[420px] w-full" title="Preview Scan BPKB"></iframe>
                        @else
                            <img src="{{ $url }}" alt="Scan BPKB {{ $kendaraan->plat_nomor }}" class="max-h-[420px] w-full object-contain">
                        @endif
                        <a href="{{ $url }}" target="_blank" class="block border-t border-slate-200 bg-white px-4 py-3 text-sm font-bold text-sky-700 hover:text-sky-900">Buka file BPKB</a>
                    @else
                        <div class="grid h-56 place-items-center text-sm text-slate-500">Belum ada file scan BPKB.</div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-base font-bold">Preview Scan STNK</h3>
                <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                    @if ($kendaraan->scan_stnk)
                        @php
                            $url = asset('storage/'.$kendaraan->scan_stnk);
                            $isPdf = str_ends_with(strtolower($kendaraan->scan_stnk), '.pdf');
                        @endphp
                        @if ($isPdf)
                            <iframe src="{{ $url }}" class="h-72 w-full" title="Preview Scan STNK"></iframe>
                        @else
                            <img src="{{ $url }}" alt="Scan STNK {{ $kendaraan->plat_nomor }}" class="max-h-72 w-full object-contain">
                        @endif
                        <a href="{{ $url }}" target="_blank" class="block border-t border-slate-200 bg-white px-4 py-3 text-sm font-bold text-sky-700 hover:text-sky-900">Buka file STNK</a>
                    @else
                        <div class="grid h-40 place-items-center text-sm text-slate-500">Belum ada scan STNK.</div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-base font-bold">Foto Kendaraan</h3>
                <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                    @if ($kendaraan->foto_kendaraan)
                    @php
                        $url = asset('storage/'.$kendaraan->foto_kendaraan);
                    @endphp
                    <img src="{{ $url }}" alt="Foto kendaraan {{ $kendaraan->plat_nomor }}" class="max-h-80 w-full object-contain">
                    <a href="{{ $url }}" target="_blank" class="block border-t border-slate-200 bg-white px-4 py-3 text-sm font-bold text-sky-700 hover:text-sky-900">Buka foto kendaraan</a>
                    @else
                        <div class="grid h-40 place-items-center text-sm text-slate-500">Belum ada foto kendaraan.</div>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</x-layouts.app>
