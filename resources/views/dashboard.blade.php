<x-layouts.app heading="Dashboard">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = auth()->user()->isAdmin()
                ? [
                    ['label' => 'Kendaraan Terverifikasi', 'value' => $stats['total'], 'tone' => 'sky'],
                    ['label' => 'Menunggu Verifikasi', 'value' => $stats['menunggu'], 'tone' => 'amber'],
                    ['label' => 'Perlu Revisi', 'value' => $stats['revisi'], 'tone' => 'emerald'],
                    ['label' => 'Ditolak', 'value' => $stats['ditolak'], 'tone' => 'rose'],
                ]
                : [
                    ['label' => 'Total Kendaraan', 'value' => $stats['total'], 'tone' => 'sky'],
                    ['label' => 'Menunggu Verifikasi', 'value' => $stats['menunggu'], 'tone' => 'amber'],
                    ['label' => 'Disetujui', 'value' => $stats['disetujui'], 'tone' => 'emerald'],
                    ['label' => 'Revisi / Ditolak', 'value' => $stats['revisi'] + $stats['ditolak'], 'tone' => 'rose'],
                ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($card['value']) }}</p>
            </div>
        @endforeach
    </div>

    @if (auth()->user()->isAdmin())
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">OPD Terdaftar</p>
                <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($stats['opd']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Akun User OPD</p>
                <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($stats['user_opd']) }}</p>
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.4fr_.8fr]">
        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-bold">Input Terbaru</h2>
                <a href="{{ route('kendaraans.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-900">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Plat</th>
                            <th class="px-5 py-3">Kendaraan</th>
                            <th class="px-5 py-3">OPD</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentKendaraan as $kendaraan)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-semibold">{{ $kendaraan->plat_nomor ?: '-' }}</td>
                                <td class="px-5 py-3">{{ $kendaraan->merk }} {{ $kendaraan->tipe }}</td>
                                <td class="px-5 py-3">{{ $kendaraan->opd?->nama }}</td>
                                <td class="px-5 py-3">@include('kendaraans.partials.status', ['status' => $kendaraan->status_verifikasi])</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada data kendaraan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold">{{ auth()->user()->isAdmin() ? 'Potensi Duplikat' : 'Ringkasan Status' }}</h2>
            @if (auth()->user()->isAdmin())
                <div class="mt-4 space-y-3">
                    @forelse ($duplicateGroups as $group)
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                            <p class="text-sm font-bold text-amber-900">{{ $group->total }} data mirip</p>
                            <p class="mt-1 text-xs text-amber-800">Rangka: {{ $group->nomor_rangka }} | Mesin: {{ $group->nomor_mesin }}</p>
                        </div>
                    @empty
                        <p class="mt-4 text-sm text-slate-500">Belum ada potensi duplikat dari nomor rangka atau nomor mesin.</p>
                    @endforelse
                </div>
            @else
                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-slate-50 p-3"><span class="block text-slate-500">Draft</span><b>{{ $stats['draft'] }}</b></div>
                    <div class="rounded-lg bg-slate-50 p-3"><span class="block text-slate-500">Revisi</span><b>{{ $stats['revisi'] }}</b></div>
                    <div class="rounded-lg bg-slate-50 p-3"><span class="block text-slate-500">Menunggu</span><b>{{ $stats['menunggu'] }}</b></div>
                    <div class="rounded-lg bg-slate-50 p-3"><span class="block text-slate-500">Disetujui</span><b>{{ $stats['disetujui'] }}</b></div>
                </div>
            @endif
        </section>
    </div>

</x-layouts.app>
