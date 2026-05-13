<x-layouts.app heading="Peminjaman BPKB">
    <div class="mb-5 grid gap-4 md:grid-cols-3">
        <a href="{{ route('admin.peminjaman-bpkbs.index', ['status' => \App\Models\PeminjamanBpkb::STATUS_DIAJUKAN]) }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-sky-200 hover:bg-sky-50">
            <p class="text-sm font-medium text-slate-500">Menunggu Verifikasi</p>
            <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($menungguVerifikasi) }}</p>
        </a>
        <a href="{{ route('admin.peminjaman-bpkbs.index', ['status' => \App\Models\PeminjamanBpkb::STATUS_DISETUJUI]) }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-sky-200 hover:bg-sky-50">
            <p class="text-sm font-medium text-slate-500">Siap Diambil</p>
            <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($siapDiambil) }}</p>
        </a>
        <a href="{{ route('admin.peminjaman-bpkbs.index', ['status' => \App\Models\PeminjamanBpkb::STATUS_DIPINJAM]) }}" class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm transition hover:bg-rose-100">
            <p class="text-sm font-medium text-rose-700">Belum Mengembalikan</p>
            <p class="mt-3 text-3xl font-black text-rose-900">{{ number_format($belumMengembalikan) }}</p>
        </a>
    </div>

    <div class="mb-5">
        <form class="grid gap-3 lg:grid-cols-[1fr_240px_auto]" method="GET">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                <input name="search" value="{{ $search }}" placeholder="Cari plat, OPD, rangka, BPKB, pengambil" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
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
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kendaraan</th>
                        <th class="px-5 py-3">OPD</th>
                        <th class="px-5 py-3">Rencana</th>
                        <th class="px-5 py-3">Pengambil</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Posisi</th>
                        <th class="px-5 py-3 text-right">Aksi Admin</th>
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
                            <td class="px-5 py-3">{{ $peminjaman->opd?->nama }}</td>
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
                                    Kantor admin, siap diambil
                                @elseif ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DIKEMBALIKAN)
                                    Kembali {{ $peminjaman->dikembalikan_at?->format('d/m/Y H:i') }}
                                @else
                                    Kantor admin
                                @endif
                                @if ($peminjaman->catatan_admin)
                                    <span class="mt-1 block text-slate-500">Catatan: {{ $peminjaman->catatan_admin }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DIAJUKAN)
                                    <form method="POST" action="{{ route('admin.peminjaman-bpkbs.update', $peminjaman) }}" class="ml-auto grid min-w-56 gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="aksi" class="h-10 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                            <option value="setujui">Setujui, BPKB siap</option>
                                            <option value="tolak">Tolak</option>
                                        </select>
                                        <input name="catatan_admin" placeholder="Catatan admin" class="h-10 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                        <button class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-slate-900 px-3 text-sm font-bold text-white transition hover:bg-slate-700">
                                            <x-icon name="check" class="h-4 w-4"/>
                                            Simpan
                                        </button>
                                    </form>
                                @elseif ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DISETUJUI)
                                    <form method="POST" action="{{ route('admin.peminjaman-bpkbs.update', $peminjaman) }}" class="ml-auto grid min-w-56 gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="aksi" value="pinjamkan">
                                        <input name="catatan_admin" placeholder="Catatan saat diserahkan" class="h-10 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                        <button class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-violet-600 px-3 text-sm font-bold text-white transition hover:bg-violet-700">
                                            <x-icon name="file-text" class="h-4 w-4"/>
                                            Tandai Dipinjam
                                        </button>
                                    </form>
                                @elseif ($peminjaman->status === \App\Models\PeminjamanBpkb::STATUS_DIPINJAM)
                                    <form method="POST" action="{{ route('admin.peminjaman-bpkbs.update', $peminjaman) }}" class="ml-auto grid min-w-56 gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="aksi" value="kembalikan">
                                        <input name="catatan_admin" placeholder="Catatan pengembalian" class="h-10 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                                        <button class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-3 text-sm font-bold text-white transition hover:bg-emerald-700">
                                            <x-icon name="check" class="h-4 w-4"/>
                                            Tandai Kembali
                                        </button>
                                    </form>
                                @else
                                    <span class="block text-right text-xs text-slate-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">Tidak ada data peminjaman BPKB pada filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $peminjamans->links() }}</div>
    </section>
</x-layouts.app>
