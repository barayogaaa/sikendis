<x-layouts.app heading="Tambah Mutasi Kendaraan">
    <div class="mb-5 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
        Pilih kendaraan yang sudah disetujui admin, tentukan OPD penerima mutasi, lalu unggah BAST Mutasi.
    </div>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('mutasi-kendaraans.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid gap-5">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Kendaraan yang Dimutasi</label>
                    <div class="relative mt-2">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                        <input id="kendaraan-mutasi-search" type="search" autocomplete="off" placeholder="Ketik plat, merk, tipe, rangka, atau mesin" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div id="kendaraan-mutasi-list" class="mt-3 max-h-80 space-y-2 overflow-y-auto rounded-lg border border-slate-200 bg-slate-50 p-2">
                        <div id="kendaraan-mutasi-hint" class="rounded-lg bg-white p-4 text-sm text-slate-500">Ketik kata kunci untuk menampilkan kendaraan yang sesuai.</div>
                        @forelse ($kendaraans as $kendaraan)
                            @php
                                $searchText = collect([
                                    $kendaraan->plat_nomor,
                                    $kendaraan->merk,
                                    $kendaraan->tipe,
                                    $kendaraan->nomor_rangka,
                                    $kendaraan->nomor_mesin,
                                ])->filter()->implode(' ');
                            @endphp
                            <label data-vehicle-option data-search="{{ strtolower($searchText) }}" class="hidden block cursor-pointer rounded-lg border border-slate-200 bg-white p-3 transition hover:border-sky-200 hover:bg-sky-50">
                                <input type="radio" name="kendaraan_id" value="{{ $kendaraan->id }}" required @checked((int) old('kendaraan_id') === $kendaraan->id) class="peer sr-only">
                                <span class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <span>
                                        <span class="block text-sm font-black text-slate-950">{{ $kendaraan->plat_nomor ?: '-' }}</span>
                                        <span class="block text-sm text-slate-600">{{ $kendaraan->merk }} {{ $kendaraan->tipe }} {{ $kendaraan->tahun ? '('.$kendaraan->tahun.')' : '' }}</span>
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        R: {{ $kendaraan->nomor_rangka ?: '-' }}<br>
                                        M: {{ $kendaraan->nomor_mesin ?: '-' }}
                                    </span>
                                </span>
                                <span class="mt-3 hidden rounded-md bg-sky-600 px-2.5 py-1 text-xs font-bold text-white peer-checked:inline-flex">Dipilih</span>
                            </label>
                        @empty
                            <div class="rounded-lg bg-white p-4 text-sm text-amber-700">Tidak ada kendaraan terverifikasi atau kendaraan sedang memiliki pengajuan mutasi aktif.</div>
                        @endforelse
                        <div id="kendaraan-mutasi-empty" class="hidden rounded-lg bg-white p-4 text-sm text-slate-500">Tidak ada kendaraan yang cocok dengan kata kunci.</div>
                    </div>
                    @if ($kendaraans->isEmpty())
                        <p class="mt-2 text-sm text-amber-700">Tidak ada kendaraan terverifikasi yang cocok atau kendaraan sedang memiliki pengajuan mutasi aktif.</p>
                    @endif
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">OPD Tujuan Mutasi</label>
                    <select name="opd_tujuan_id" required class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                        <option value="">Pilih OPD tujuan</option>
                        @foreach ($opds as $opd)
                            <option value="{{ $opd->id }}" @selected((int) old('opd_tujuan_id') === $opd->id)>{{ $opd->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Nomor BAST</label>
                        <input name="nomor_bast" value="{{ old('nomor_bast') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Tanggal BAST</label>
                        <input type="date" name="tanggal_bast" value="{{ old('tanggal_bast') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">Upload BAST Mutasi</label>
                    <div class="mt-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4">
                        <input type="file" name="file_bast" required accept=".pdf,.jpg,.jpeg,.png,.webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-sky-700">
                        <p class="mt-2 text-xs text-slate-500">Format PDF/JPG/PNG/WebP, maksimal 5 MB.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('mutasi-kendaraans.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-100">Batal</a>
                <button class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">
                    <x-icon name="upload" class="h-4 w-4"/>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('kendaraan-mutasi-search');
            const options = Array.from(document.querySelectorAll('[data-vehicle-option]'));
            const empty = document.getElementById('kendaraan-mutasi-empty');
            const hint = document.getElementById('kendaraan-mutasi-hint');

            if (!search || !empty || !hint) {
                return;
            }

            search.addEventListener('input', () => {
                const keyword = search.value.trim().toLowerCase();
                let visible = 0;

                options.forEach((option) => {
                    option.querySelector('input[type="radio"]').checked = false;
                });

                if (keyword === '') {
                    options.forEach((option) => option.classList.add('hidden'));
                    empty.classList.add('hidden');
                    hint.classList.remove('hidden');
                    return;
                }

                hint.classList.add('hidden');

                options.forEach((option) => {
                    const matched = option.dataset.search.includes(keyword);
                    option.classList.toggle('hidden', !matched);

                    if (matched) {
                        visible += 1;
                    }
                });

                empty.classList.toggle('hidden', visible > 0);
            });

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    options.forEach((item) => {
                        if (item !== option) {
                            item.classList.add('hidden');
                        }
                    });

                    empty.classList.add('hidden');
                    hint.classList.add('hidden');
                });
            });
        });
    </script>
</x-layouts.app>
