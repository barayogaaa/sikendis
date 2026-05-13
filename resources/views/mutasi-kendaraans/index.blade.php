<x-layouts.app heading="Mutasi Kendaraan">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="relative w-full sm:max-w-md">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
            <input name="search" value="{{ $search }}" placeholder="Cari plat, rangka, mesin, OPD tujuan" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </form>
        <a href="{{ route('mutasi-kendaraans.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">
            <x-icon name="plus" class="h-4 w-4"/>
            Tambah Kendaraan
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kendaraan</th>
                        <th class="px-5 py-3">OPD Tujuan</th>
                        <th class="px-5 py-3">BAST</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Catatan Admin</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mutasis as $mutasi)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <b>{{ $mutasi->kendaraan?->plat_nomor ?: '-' }}</b>
                                <span class="block text-xs text-slate-500">{{ $mutasi->kendaraan?->merk }} {{ $mutasi->kendaraan?->tipe }}</span>
                            </td>
                            <td class="px-5 py-3">{{ $mutasi->opdTujuan?->nama }}</td>
                            <td class="px-5 py-3">
                                <span class="block font-semibold">{{ $mutasi->nomor_bast ?: '-' }}</span>
                                <span class="block text-xs text-slate-500">{{ $mutasi->tanggal_bast?->format('d/m/Y') ?: '-' }}</span>
                                <a href="{{ asset('storage/'.$mutasi->file_bast) }}" target="_blank" class="mt-1 inline-flex text-xs font-bold text-sky-700 hover:text-sky-900">Lihat BAST</a>
                            </td>
                            <td class="px-5 py-3">@include('mutasi-kendaraans.partials.status', ['status' => $mutasi->status])</td>
                            <td class="max-w-xs px-5 py-3 text-slate-600">{{ $mutasi->catatan_admin ?: '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    @if ($mutasi->status === \App\Models\MutasiKendaraan::STATUS_MENUNGGU)
                                        <form method="POST" action="{{ route('mutasi-kendaraans.destroy', $mutasi) }}" onsubmit="return confirm('Batalkan pengajuan mutasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700" title="Batalkan">
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
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">Belum ada pengajuan mutasi kendaraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $mutasis->links() }}</div>
    </section>
</x-layouts.app>
