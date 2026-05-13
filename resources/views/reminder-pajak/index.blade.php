<x-layouts.app heading="Pengingat Pajak">
    <div class="mb-5 grid gap-4 md:grid-cols-3">
        <a href="{{ route('reminder-pajak.index', ['kategori' => 'tenggat']) }}" class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition hover:bg-amber-100">
            <p class="text-sm font-medium text-amber-700">Dalam Jatuh Tempo</p>
            <p class="mt-3 text-3xl font-black text-amber-950">{{ number_format($stats['tenggat']) }}</p>
        </a>
        <a href="{{ route('reminder-pajak.index', ['kategori' => 'telat']) }}" class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm transition hover:bg-rose-100">
            <p class="text-sm font-medium text-rose-700">Telat Bayar STNK</p>
            <p class="mt-3 text-3xl font-black text-rose-950">{{ number_format($stats['telat']) }}</p>
        </a>
        <a href="{{ route('reminder-pajak.index', ['kategori' => 'semua']) }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-sky-200 hover:bg-sky-50">
            <p class="text-sm font-medium text-slate-500">Kendaraan Bertanggal STNK</p>
            <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($stats['total']) }}</p>
        </a>
    </div>

    <section class="mb-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" class="grid gap-3 lg:grid-cols-[1fr_240px_auto]">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                <input name="search" value="{{ $search }}" placeholder="Cari plat, merk, rangka, mesin, BPKB, OPD" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
            </div>
            <select name="kategori" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                <option value="tenggat" @selected($kategori === 'tenggat')>Dalam jatuh tempo</option>
                <option value="telat" @selected($kategori === 'telat')>Telat bayar STNK</option>
                <option value="semua" @selected($kategori === 'semua')>Semua bertanggal STNK</option>
            </select>
            <button class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 text-sm font-bold text-white hover:bg-slate-700">
                <x-icon name="filter" class="h-4 w-4"/>
                Filter
            </button>
        </form>
        <p class="mt-3 text-sm text-slate-500">
            Pengingat aktif mulai H-21 sebelum tanggal STNK sampai hari H tanggal STNK. Untuk hari ini, kendaraan yang tampil adalah kendaraan dengan tanggal STNK {{ $today->format('d/m/Y') }} sampai {{ $reminderUntil->format('d/m/Y') }}.
        </p>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kendaraan</th>
                        <th class="px-5 py-3">OPD</th>
                        <th class="px-5 py-3">Tanggal STNK</th>
                        <th class="px-5 py-3">Status Pajak</th>
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kendaraans as $kendaraan)
                        @php
                            $tanggalStnk = $kendaraan->tanggal_stnk?->startOfDay();
                            $days = $tanggalStnk ? (int) $today->diffInDays($tanggalStnk, false) : null;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <b>{{ $kendaraan->plat_nomor ?: '-' }}</b>
                                <span class="block text-xs text-slate-500">{{ $kendaraan->merk }} {{ $kendaraan->tipe }} {{ $kendaraan->tahun ? '('.$kendaraan->tahun.')' : '' }}</span>
                                <span class="block text-xs text-slate-500">BPKB: {{ $kendaraan->nomor_bpkb ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3">{{ $kendaraan->opd?->nama ?: '-' }}</td>
                            <td class="px-5 py-3 font-semibold">{{ $kendaraan->tanggal_stnk?->format('d/m/Y') ?: '-' }}</td>
                            <td class="px-5 py-3">
                                @if ($days !== null && $days < 0)
                                    <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700">Telat {{ abs($days) }} hari</span>
                                @elseif ($days === 0)
                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Jatuh tempo hari ini</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">{{ $days }} hari lagi</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                {{ $kendaraan->pengguna_penanggung_jawab ?: '-' }}
                                <span class="block text-xs text-slate-500">{{ $kendaraan->nip_pengguna_penanggung_jawab ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('kendaraans.show', $kendaraan) }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                    <x-icon name="eye" class="h-4 w-4"/>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">Tidak ada kendaraan pada kategori pengingat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $kendaraans->links() }}</div>
    </section>
</x-layouts.app>
