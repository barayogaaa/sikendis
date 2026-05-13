@csrf
@if ($kendaraan->exists)
    @method('PUT')
@endif

@php
    $lockedForPenggunaOnly = $kendaraan->exists && $kendaraan->canOnlyEditPenggunaBy(auth()->user());
    $lockedForReferensi = $kendaraan->exists && $kendaraan->referensi_kendaraan_id;
@endphp

@if ($lockedForPenggunaOnly)
    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Data kendaraan sudah diverifikasi admin. User OPD hanya dapat mengubah Pengguna/Penanggung Jawab, NIP, Tanggal STNK, dan Scan STNK.
    </div>
@endif

@if ($lockedForReferensi && ! $lockedForPenggunaOnly)
    <div class="mb-5 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
        Data kendaraan berasal dari database import. Plat, merk, tipe, tahun, nomor rangka, nomor mesin, dan nomor BPKB dikunci agar tetap sesuai data awal.
    </div>
@endif

@if (! $kendaraan->exists)
    <section class="mb-5 rounded-xl border border-sky-200 bg-sky-50 p-4">
        <h2 class="text-sm font-bold text-sky-950">Metode Input Kendaraan</h2>
        <p class="mt-1 text-sm text-sky-800">Pilih dari database import jika kendaraan sudah tersedia. Jika BPKB baru belum ada di database, gunakan input manual.</p>

        <div class="mt-4 flex flex-col gap-2 sm:flex-row">
            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-sky-200 bg-white px-3 py-2 text-sm font-bold text-sky-900">
                <input type="radio" name="input_mode" value="referensi" class="text-sky-600 focus:ring-sky-500" @checked(old('input_mode', 'referensi') === 'referensi')>
                Cari dari Database
            </label>
            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-sky-200 bg-white px-3 py-2 text-sm font-bold text-sky-900">
                <input type="radio" name="input_mode" value="manual" class="text-sky-600 focus:ring-sky-500" @checked(old('input_mode') === 'manual')>
                Input Manual
            </label>
        </div>

        <input type="hidden" name="referensi_kendaraan_id" id="referensi-kendaraan-id" value="{{ old('referensi_kendaraan_id') }}">

        <div id="referensi-kendaraan-panel">
            <div class="relative mt-4">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                <input id="referensi-kendaraan-search" type="search" autocomplete="off" placeholder="Cari kendaraan..." class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
            </div>
            <div id="referensi-kendaraan-results" class="mt-3 hidden max-h-80 space-y-2 overflow-y-auto rounded-lg border border-slate-200 bg-white p-2"></div>
            <div id="referensi-kendaraan-selected" class="mt-3 hidden rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-bold" data-selected-title></p>
                        <p class="mt-1 text-xs" data-selected-detail></p>
                    </div>
                    <button type="button" id="referensi-kendaraan-clear" class="inline-flex h-9 items-center justify-center rounded-lg border border-emerald-200 bg-white px-3 text-xs font-bold text-emerald-800 hover:bg-emerald-100">Ganti pilihan</button>
                </div>
            </div>
        </div>
    </section>
@endif

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-slate-700">Plat Nomor</label>
        <input name="plat_nomor" data-referensi-field="plat_nomor" value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}" @readonly($lockedForReferensi) @disabled($lockedForPenggunaOnly) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 read-only:bg-slate-100 read-only:text-slate-500 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Merk</label>
        <input name="merk" data-referensi-field="merk" value="{{ old('merk', $kendaraan->merk) }}" required @readonly($lockedForReferensi) @disabled($lockedForPenggunaOnly) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 read-only:bg-slate-100 read-only:text-slate-500 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Tipe</label>
        <input name="tipe" data-referensi-field="tipe" value="{{ old('tipe', $kendaraan->tipe) }}" @readonly($lockedForReferensi) @disabled($lockedForPenggunaOnly) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 read-only:bg-slate-100 read-only:text-slate-500 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Tahun</label>
        <input name="tahun" data-referensi-field="tahun" type="number" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('tahun', $kendaraan->tahun) }}" @readonly($lockedForReferensi) @disabled($lockedForPenggunaOnly) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 read-only:bg-slate-100 read-only:text-slate-500 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nomor Rangka</label>
        <input name="nomor_rangka" data-referensi-field="nomor_rangka" value="{{ old('nomor_rangka', $kendaraan->nomor_rangka) }}" @readonly($lockedForReferensi) @disabled($lockedForPenggunaOnly) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 read-only:bg-slate-100 read-only:text-slate-500 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nomor Mesin</label>
        <input name="nomor_mesin" data-referensi-field="nomor_mesin" value="{{ old('nomor_mesin', $kendaraan->nomor_mesin) }}" @readonly($lockedForReferensi) @disabled($lockedForPenggunaOnly) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 read-only:bg-slate-100 read-only:text-slate-500 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nomor BPKB</label>
        <input name="nomor_bpkb" data-referensi-field="nomor_bpkb" value="{{ old('nomor_bpkb', $kendaraan->nomor_bpkb) }}" @readonly($lockedForReferensi) @disabled($lockedForPenggunaOnly) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 read-only:bg-slate-100 read-only:text-slate-500 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Tanggal STNK</label>
        <input type="date" name="tanggal_stnk" value="{{ old('tanggal_stnk', $kendaraan->tanggal_stnk?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 disabled:bg-slate-100 disabled:text-slate-500">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Pengguna / Penanggung Jawab</label>
        <input name="pengguna_penanggung_jawab" value="{{ old('pengguna_penanggung_jawab', $kendaraan->pengguna_penanggung_jawab) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">NIP Pengguna / Penanggung Jawab</label>
        <input name="nip_pengguna_penanggung_jawab" value="{{ old('nip_pengguna_penanggung_jawab', $kendaraan->nip_pengguna_penanggung_jawab) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Scan BPKB</label>
        <div class="mt-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4">
            <input type="file" name="scan_bpkb" data-upload-field="scan_bpkb" accept=".pdf,.jpg,.jpeg,.png,.webp" @disabled($lockedForPenggunaOnly) class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-sky-700 disabled:text-slate-400">
            <p class="mt-2 text-xs text-slate-500">Format PDF/JPG/PNG/WebP, maksimal 5 MB.</p>
            @if ($kendaraan->scan_bpkb)
                <a href="{{ asset('storage/'.$kendaraan->scan_bpkb) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-900">
                    <x-icon name="eye" class="h-4 w-4"/>
                    Lihat scan saat ini
                </a>
            @endif
        </div>
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Scan STNK</label>
        <div class="mt-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4">
            <input type="file" name="scan_stnk" data-upload-field="scan_stnk" accept=".pdf,.jpg,.jpeg,.png,.webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-sky-700 disabled:text-slate-400">
            <p class="mt-2 text-xs text-slate-500">Format PDF/JPG/PNG/WebP, maksimal 5 MB.</p>
            @if ($kendaraan->scan_stnk)
                <a href="{{ asset('storage/'.$kendaraan->scan_stnk) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-900">
                    <x-icon name="eye" class="h-4 w-4"/>
                    Lihat scan STNK saat ini
                </a>
            @endif
        </div>
    </div>
    <div class="lg:col-span-2">
        <label class="text-sm font-semibold text-slate-700">Foto Kendaraan</label>
        <div class="mt-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4">
            <input type="file" name="foto_kendaraan" data-upload-field="foto_kendaraan" accept=".jpg,.jpeg,.png,.webp" @disabled($lockedForPenggunaOnly) class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-sky-700 disabled:text-slate-400">
            <p class="mt-2 text-xs text-slate-500">Format JPG/PNG/WebP, maksimal 5 MB.</p>
            @if ($kendaraan->foto_kendaraan)
                <a href="{{ asset('storage/'.$kendaraan->foto_kendaraan) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-900">
                    <x-icon name="eye" class="h-4 w-4"/>
                    Lihat foto kendaraan saat ini
                </a>
            @endif
        </div>
    </div>
</div>

@if (! $kendaraan->exists)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('referensi-kendaraan-search');
            const results = document.getElementById('referensi-kendaraan-results');
            const selected = document.getElementById('referensi-kendaraan-selected');
            const selectedTitle = selected?.querySelector('[data-selected-title]');
            const selectedDetail = selected?.querySelector('[data-selected-detail]');
            const clearButton = document.getElementById('referensi-kendaraan-clear');
            const referensiId = document.getElementById('referensi-kendaraan-id');
            const panel = document.getElementById('referensi-kendaraan-panel');
            const modeInputs = document.querySelectorAll('input[name="input_mode"]');
            const fields = document.querySelectorAll('[data-referensi-field]');
            const scanBpkb = document.querySelector('[data-upload-field="scan_bpkb"]');
            const scanStnk = document.querySelector('[data-upload-field="scan_stnk"]');
            const fotoKendaraan = document.querySelector('[data-upload-field="foto_kendaraan"]');
            let controller;

            const selectedMode = () => document.querySelector('input[name="input_mode"]:checked')?.value ?? 'referensi';

            const setIdentityLocked = (locked) => {
                fields.forEach((field) => {
                    field.readOnly = locked;
                });
            };

            const setUploadRequirements = (mode) => {
                if (scanBpkb) scanBpkb.required = mode === 'manual';
                if (scanStnk) scanStnk.required = mode === 'referensi';
                if (fotoKendaraan) fotoKendaraan.required = mode === 'referensi';
            };

            const clearSelection = () => {
                referensiId.value = '';
                selected.classList.add('hidden');
                fields.forEach((field) => {
                    field.value = '';
                });
                setIdentityLocked(true);
            };

            const setMode = () => {
                const mode = selectedMode();

                setUploadRequirements(mode);

                if (mode === 'manual') {
                    panel.classList.add('hidden');
                    referensiId.value = '';
                    selected.classList.add('hidden');
                    results.classList.add('hidden');
                    results.innerHTML = '';
                    search.value = '';
                    setIdentityLocked(false);
                    return;
                }

                panel.classList.remove('hidden');
                setIdentityLocked(true);

                if (! referensiId.value) {
                    clearSelection();
                }
            };

            const choose = (item) => {
                referensiId.value = item.id;
                fields.forEach((field) => {
                    field.value = item[field.dataset.referensiField] ?? '';
                    field.readOnly = true;
                });
                selectedTitle.textContent = `${item.plat_nomor ?? '-'} - ${item.merk ?? ''} ${item.tipe ?? ''}`.trim();
                selectedDetail.textContent = `Tahun: ${item.tahun ?? '-'} | Rangka: ${item.nomor_rangka ?? '-'} | Mesin: ${item.nomor_mesin ?? '-'} | BPKB: ${item.nomor_bpkb ?? '-'}`;
                selected.classList.remove('hidden');
                results.classList.add('hidden');
                results.innerHTML = '';
                search.value = '';
            };

            const render = (items) => {
                results.innerHTML = '';

                if (items.length === 0) {
                    results.innerHTML = '<div class="rounded-lg p-3 text-sm text-slate-500">Tidak ada kendaraan yang cocok atau kendaraan sudah dipilih OPD.</div>';
                    results.classList.remove('hidden');
                    return;
                }

                items.forEach((item) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'block w-full rounded-lg border border-slate-200 bg-white p-3 text-left transition hover:border-sky-200 hover:bg-sky-50';
                    button.innerHTML = `
                        <span class="block text-sm font-black text-slate-950">${item.plat_nomor ?? '-'}</span>
                        <span class="block text-sm text-slate-600">${item.merk ?? ''} ${item.tipe ?? ''} ${item.tahun ? '(' + item.tahun + ')' : ''}</span>
                        <span class="mt-2 block text-xs text-slate-500">R: ${item.nomor_rangka ?? '-'} | M: ${item.nomor_mesin ?? '-'} | BPKB: ${item.nomor_bpkb ?? '-'}</span>
                    `;
                    button.addEventListener('click', () => choose(item));
                    results.appendChild(button);
                });

                results.classList.remove('hidden');
            };

            search?.addEventListener('input', () => {
                const q = search.value.trim();

                if (controller) {
                    controller.abort();
                }

                if (q.length < 2) {
                    results.classList.add('hidden');
                    results.innerHTML = '';
                    return;
                }

                controller = new AbortController();

                fetch(`{{ route('referensi-kendaraans.search') }}?q=${encodeURIComponent(q)}`, {
                    headers: {'Accept': 'application/json'},
                    signal: controller.signal,
                })
                    .then((response) => response.json())
                    .then(render)
                    .catch((error) => {
                        if (error.name !== 'AbortError') {
                            results.innerHTML = '<div class="rounded-lg p-3 text-sm text-rose-600">Gagal mencari data referensi.</div>';
                            results.classList.remove('hidden');
                        }
                    });
            });

            clearButton?.addEventListener('click', clearSelection);
            modeInputs.forEach((input) => input.addEventListener('change', setMode));

            setMode();
        });
    </script>
@endif

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ $kendaraan->exists ? route('kendaraans.show', $kendaraan) : route('kendaraans.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-100">Batal</a>
    <button class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">
        <x-icon name="check" class="h-4 w-4"/>
        {{ $lockedForPenggunaOnly ? 'Simpan Perubahan' : 'Simpan Draft' }}
    </button>
</div>
