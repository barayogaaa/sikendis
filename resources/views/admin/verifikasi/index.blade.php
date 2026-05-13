<x-layouts.app heading="Verifikasi Kendaraan">
    <div class="mb-5 flex gap-2 overflow-x-auto">
        <a href="{{ route('admin.verifikasi.index', ['jenis' => 'bpkb']) }}" class="inline-flex h-10 items-center rounded-lg px-4 text-sm font-bold transition {{ $jenis === 'bpkb' ? 'bg-sky-600 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-100' }}">
            Input BPKB Baru
        </a>
        <a href="{{ route('admin.verifikasi.index', ['jenis' => 'mutasi']) }}" class="inline-flex h-10 items-center rounded-lg px-4 text-sm font-bold transition {{ $jenis === 'mutasi' ? 'bg-sky-600 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-100' }}">
            Mutasi Kendaraan
        </a>
    </div>

    <div class="mb-5">
        <form class="grid gap-3 sm:grid-cols-[1fr_240px_auto]" method="GET">
            <input type="hidden" name="jenis" value="{{ $jenis }}">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                <input name="search" value="{{ $search }}" placeholder="{{ $jenis === 'mutasi' ? 'Cari plat, rangka, mesin, OPD asal/tujuan' : 'Cari plat, rangka, mesin, OPD' }}" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
            </div>
            <select name="status" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 text-sm font-bold text-white hover:bg-slate-700"><x-icon name="filter" class="h-4 w-4"/> Filter</button>
        </form>
    </div>

    @if ($jenis === 'mutasi')
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Kendaraan</th>
                            <th class="px-5 py-3">OPD Asal</th>
                            <th class="px-5 py-3">OPD Tujuan</th>
                            <th class="px-5 py-3">BAST</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($mutasis as $mutasi)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <b>{{ $mutasi->kendaraan?->plat_nomor ?: '-' }}</b>
                                    <span class="block text-xs text-slate-500">{{ $mutasi->kendaraan?->merk }} {{ $mutasi->kendaraan?->tipe }}</span>
                                    <span class="block text-xs text-slate-500">R: {{ $mutasi->kendaraan?->nomor_rangka ?: '-' }}</span>
                                </td>
                                <td class="px-5 py-3">{{ $mutasi->opdAsal?->nama }}</td>
                                <td class="px-5 py-3">{{ $mutasi->opdTujuan?->nama }}</td>
                                <td class="px-5 py-3">
                                    <span class="block font-semibold">{{ $mutasi->nomor_bast ?: '-' }}</span>
                                    <span class="block text-xs text-slate-500">{{ $mutasi->tanggal_bast?->format('d/m/Y') ?: '-' }}</span>
                                    <a href="{{ asset('storage/'.$mutasi->file_bast) }}" target="_blank" class="mt-1 inline-flex text-xs font-bold text-sky-700 hover:text-sky-900">Lihat BAST</a>
                                </td>
                                <td class="px-5 py-3">@include('mutasi-kendaraans.partials.status', ['status' => $mutasi->status])</td>
                                <td class="px-5 py-3">
                                    @if ($mutasi->status === \App\Models\MutasiKendaraan::STATUS_MENUNGGU)
                                        <form method="POST" action="{{ route('admin.verifikasi-mutasi.update', $mutasi) }}" class="ml-auto grid max-w-sm gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="h-10 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                                <option value="disetujui">Setujui Mutasi</option>
                                                <option value="ditolak">Tolak Mutasi</option>
                                            </select>
                                            <input name="catatan_admin" placeholder="Catatan admin" class="h-10 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                            <button class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-slate-900 px-3 text-sm font-bold text-white transition hover:bg-slate-700">
                                                <x-icon name="check" class="h-4 w-4"/>
                                                Simpan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-sm text-slate-500">{{ $mutasi->catatan_admin ?: '-' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">Tidak ada mutasi kendaraan pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4">{{ $mutasis->links() }}</div>
        </section>
    @else
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Kendaraan</th><th class="px-5 py-3">OPD</th><th class="px-5 py-3">Rangka / Mesin</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kendaraans as $kendaraan)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3"><b>{{ $kendaraan->plat_nomor ?: '-' }}</b><span class="block text-xs text-slate-500">{{ $kendaraan->merk }} {{ $kendaraan->tipe }}</span></td>
                                <td class="px-5 py-3">{{ $kendaraan->opd?->nama }}</td>
                                <td class="px-5 py-3 text-xs"><span class="block">R: {{ $kendaraan->nomor_rangka ?: '-' }}</span><span class="block">M: {{ $kendaraan->nomor_mesin ?: '-' }}</span></td>
                                <td class="px-5 py-3">@include('kendaraans.partials.status', ['status' => $kendaraan->status_verifikasi])</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('kendaraans.show', $kendaraan) }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 px-3 text-sm font-bold text-slate-700 hover:bg-slate-50"><x-icon name="eye" class="h-4 w-4"/> Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">Tidak ada kendaraan pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4">{{ $kendaraans->links() }}</div>
        </section>
    @endif
</x-layouts.app>
