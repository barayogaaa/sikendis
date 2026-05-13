@php
    $classes = [
        \App\Models\PeminjamanBpkb::STATUS_DIAJUKAN => 'bg-amber-50 text-amber-700',
        \App\Models\PeminjamanBpkb::STATUS_DISETUJUI => 'bg-sky-50 text-sky-700',
        \App\Models\PeminjamanBpkb::STATUS_DITOLAK => 'bg-rose-50 text-rose-700',
        \App\Models\PeminjamanBpkb::STATUS_DIPINJAM => 'bg-violet-50 text-violet-700',
        \App\Models\PeminjamanBpkb::STATUS_DIKEMBALIKAN => 'bg-emerald-50 text-emerald-700',
    ];
@endphp

<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $classes[$status] ?? 'bg-slate-100 text-slate-600' }}">
    {{ \App\Models\PeminjamanBpkb::STATUS_LABELS[$status] ?? ucfirst((string) $status) }}
</span>
