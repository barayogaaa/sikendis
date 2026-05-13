<x-layouts.app heading="Import Database">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Referensi</p>
            <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($totalReferensi) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Sudah Dipilih OPD</p>
            <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($sudahDipakai) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Belum Dipilih</p>
            <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($totalReferensi - $sudahDipakai) }}</p>
        </div>
    </div>

    <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-base font-bold">Upload File Excel</h2>
                <p class="mt-1 text-sm text-slate-500">Kolom yang dibaca: Plat Nomor, Merk, Tipe, Tahun, Nomor Rangka, Nomor Mesin, Nomor BPKB. Unduh format agar susunan kolom tetap rapi saat diisi lewat Excel.</p>
            </div>
            <a href="{{ route('admin.import-referensi.template') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-slate-200 px-3 text-sm font-bold text-sky-700 transition hover:bg-sky-50">
                <x-icon name="download" class="h-4 w-4"/>
                Download Format Excel
            </a>
        </div>

        <form method="POST" action="{{ route('admin.import-referensi.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 md:grid-cols-[1fr_auto]">
            @csrf
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4">
                <input type="file" name="file_import" required accept=".xlsx,.csv" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-sky-700">
                <p class="mt-2 text-xs text-slate-500">Format yang didukung: XLSX atau CSV. Baris pertama harus berisi judul kolom.</p>
            </div>
            <button class="inline-flex h-11 items-center justify-center gap-2 self-start rounded-lg bg-sky-600 px-4 text-sm font-bold text-white transition hover:bg-sky-700">
                <x-icon name="upload" class="h-4 w-4"/>
                Import Database
            </button>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-bold">Data Import Kendaraan</h2>
                <p class="mt-1 text-sm text-slate-500">Cari, edit, atau hapus data referensi hasil import.</p>
            </div>
            <form method="GET" class="relative w-full lg:max-w-md">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                <input name="search" value="{{ $search }}" placeholder="Cari plat, merk, rangka, mesin, BPKB" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Plat</th>
                        <th class="px-5 py-3">Kendaraan</th>
                        <th class="px-5 py-3">Tahun</th>
                        <th class="px-5 py-3">Rangka / Mesin</th>
                        <th class="px-5 py-3">BPKB</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($referensis as $referensi)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-bold">{{ $referensi->plat_nomor ?: '-' }}</td>
                            <td class="px-5 py-3">{{ $referensi->merk ?: '-' }} {{ $referensi->tipe }}</td>
                            <td class="px-5 py-3">{{ $referensi->tahun ?: '-' }}</td>
                            <td class="px-5 py-3 text-xs text-slate-600">
                                <span class="block">R: {{ $referensi->nomor_rangka ?: '-' }}</span>
                                <span class="block">M: {{ $referensi->nomor_mesin ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3">{{ $referensi->nomor_bpkb ?: '-' }}</td>
                            <td class="px-5 py-3">
                                @if ($referensi->kendaraan_count)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Dipilih OPD</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Belum dipilih</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.import-referensi.edit', $referensi) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50" title="Edit data">
                                        <x-icon name="edit" class="h-4 w-4"/>
                                    </a>
                                    <form method="POST" action="{{ route('admin.import-referensi.destroy', $referensi) }}" onsubmit="return confirm('Hapus data referensi kendaraan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button @disabled($referensi->kendaraan_count > 0) class="grid h-9 w-9 place-items-center rounded-lg border border-rose-200 text-rose-600 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:border-slate-200 disabled:text-slate-300 disabled:hover:bg-white" title="{{ $referensi->kendaraan_count ? 'Sudah dipilih OPD' : 'Hapus data' }}">
                                            <x-icon name="trash" class="h-4 w-4"/>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">Belum ada data referensi kendaraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $referensis->links() }}</div>
    </section>
</x-layouts.app>
