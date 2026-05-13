@php
    $classes = [
        'draft' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'menunggu_verifikasi' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'disetujui' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'revisi' => 'bg-sky-100 text-sky-800 ring-sky-200',
        'ditolak' => 'bg-rose-100 text-rose-800 ring-rose-200',
    ][$status] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $classes }}">
    {{ \App\Models\Kendaraan::STATUS_LABELS[$status] ?? $status }}
</span>
