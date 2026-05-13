<x-layouts.app :heading="auth()->user()->isAdmin() ? 'Data Kendaraan' : 'Kendaraan Saya'">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form class="grid gap-3 sm:grid-cols-[1fr_220px_auto]" method="GET">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
                <input name="search" value="{{ $search }}" placeholder="Cari plat, rangka, mesin, BPKB, NIP, OPD" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
            </div>
            <select name="status" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                <option value="">Semua status</option>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 text-sm font-bold text-white transition hover:bg-slate-700">
                <x-icon name="filter" class="h-4 w-4"/>
                Filter
            </button>
        </form>

        @if (auth()->user()->isUserOpd())
            <a href="{{ route('kendaraans.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="plus" class="h-4 w-4"/>
                Input Kendaraan
            </a>
        @else
            <a href="{{ route('admin.kendaraans.export', request()->only(['search', 'status'])) }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
                <x-icon name="download" class="h-4 w-4"/>
                Export Excel
            </a>
        @endif
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Plat Nomor</th>
                        <th class="px-5 py-3">Kendaraan</th>
                        <th class="px-5 py-3">OPD</th>
                        <th class="px-5 py-3">Rangka / Mesin</th>
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kendaraans as $kendaraan)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3 font-bold text-slate-950">{{ $kendaraan->plat_nomor ?: '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="font-semibold">{{ $kendaraan->merk }}</span>
                                <span class="block text-xs text-slate-500">{{ $kendaraan->tipe ?: '-' }} {{ $kendaraan->tahun ? '('.$kendaraan->tahun.')' : '' }}</span>
                            </td>
                            <td class="px-5 py-3">{{ $kendaraan->opd?->nama }}</td>
                            <td class="px-5 py-3 text-xs text-slate-600">
                                <span class="block">R: {{ $kendaraan->nomor_rangka ?: '-' }}</span>
                                <span class="block">M: {{ $kendaraan->nomor_mesin ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="block font-semibold">{{ $kendaraan->pengguna_penanggung_jawab ?: '-' }}</span>
                                <span class="block text-xs text-slate-500">NIP: {{ $kendaraan->nip_pengguna_penanggung_jawab ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3">@include('kendaraans.partials.status', ['status' => $kendaraan->status_verifikasi])</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('kendaraans.show', $kendaraan) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700" title="Lihat detail">
                                        <x-icon name="eye" class="h-4 w-4"/>
                                    </a>
                                    @if ($kendaraan->canBeEditedBy(auth()->user()))
                                        <a href="{{ route('kendaraans.edit', $kendaraan) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-700" title="Edit">
                                            <x-icon name="edit" class="h-4 w-4"/>
                                        </a>
                                    @endif
                                    @if ($kendaraan->canBeDeletedBy(auth()->user()))
                                        <form method="POST" action="{{ route('kendaraans.destroy', $kendaraan) }}" onsubmit="return confirm('Hapus data kendaraan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700" title="Hapus">
                                                <x-icon name="trash" class="h-4 w-4"/>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">Belum ada data kendaraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $kendaraans->links() }}</div>
    </section>
</x-layouts.app>
