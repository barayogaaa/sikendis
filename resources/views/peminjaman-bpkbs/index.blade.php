<x-layouts.app heading="Peminjaman BPKB">
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" class="grid gap-3 lg:grid-cols-[1fr_220px_auto] lg:items-center">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                <input name="search" value="{{ $search }}" placeholder="Cari plat, rangka, BPKB, pengambil" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
            </div>
            <select name="status" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                <option value="">Semua status</option>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 text-sm font-bold text-white hover:bg-slate-700">
                <x-icon name="filter" class="h-4 w-4"/>
                Filter
            </button>
        </form>
        <a href="{{ route('peminjaman-bpkbs.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">
            <x-icon name="plus" class="h-4 w-4"/>
            Ajukan Peminjaman
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kendaraan</th>
                        <th class="px-5 py-3">Rencana</th>
                        <th class="px-5 py-3">Pengambil</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Posisi BPKB</th>
                        <th class="px-5 py-3">Catatan Admin</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($peminjamans as $peminjaman)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <b>{{ $peminjaman->kendaraan?->plat_nomor ?: '-' }}</b>
                                <span class="block text-xs text-slate-500">{{ $peminjaman->kendaraan?->merk }} {{ $peminjaman->kendaraan?->tipe }}</span>
                                <span class="block text-xs text-slate-500">BPKB: {{ $peminjaman->kendaraan?->nomor_bpkb ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="block font-semibold">{{ $peminjaman->tanggal_rencana_pinjam?->format('d/m/Y') }}</span>
                                <span class="block text-xs text-slate-500">Kembali: menunggu proses pajak</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $peminjaman->keperluan }}</span>
                            </td>
                            <td class="px-5 py-3">
                                {{ $peminjaman->nama_pengambil }}
                                <span class="block text-xs text-slate-500">{{ $peminjaman->nip_pengambil ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3">@include('peminjaman-bpkbs.partials.status', ['status' => $peminjaman->status])</td>
                            <td class="px-5 py-3 text-xs text-slate-600">
                                @if ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DIPINJAM)
                                    Di OPD sejak {{ $peminjaman->dipinjamkan_at?->format('d/m/Y H:i') }}
                                @elseif ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DISETUJUI)
                                    Siap diambil di kantor admin
                                @elseif ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DIKEMBALIKAN)
                                    Sudah kembali {{ $peminjaman->dikembalikan_at?->format('d/m/Y H:i') }}
                                @else
                                    Kantor admin
                                @endif
                            </td>
                            <td class="max-w-xs px-5 py-3 text-slate-600">{{ $peminjaman->catatan_admin ?: '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    @if ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DIAJUKAN)
                                        <form method="POST" action="{{ route('peminjaman-bpkbs.destroy', $peminjaman) }}" onsubmit="return confirm('Batalkan pengajuan peminjaman BPKB ini?')">
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
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">Belum ada pengajuan peminjaman BPKB.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $peminjamans->links() }}</div>
    </section>
</x-layouts.app>
