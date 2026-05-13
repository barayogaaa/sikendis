<x-layouts.app heading="Detail OPD">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">{{ $opd->kode ?: 'Tanpa kode' }}</p>
                <h2 class="text-2xl font-black">{{ $opd->nama }}</h2>
            </div>
            <a href="{{ route('admin.opds.edit', $opd) }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 px-3 text-sm font-bold hover:bg-slate-100"><x-icon name="edit" class="h-4 w-4"/> Edit</a>
        </div>
        <dl class="mt-5 grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-bold uppercase text-slate-500">Penanggung Jawab</dt><dd class="mt-1 font-semibold">{{ $opd->penanggung_jawab ?: '-' }}</dd></div>
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-bold uppercase text-slate-500">Telepon</dt><dd class="mt-1 font-semibold">{{ $opd->telepon ?: '-' }}</dd></div>
            <div class="rounded-lg bg-slate-50 p-4 sm:col-span-2"><dt class="text-xs font-bold uppercase text-slate-500">Alamat</dt><dd class="mt-1 font-semibold">{{ $opd->alamat ?: '-' }}</dd></div>
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-bold uppercase text-slate-500">User OPD</dt><dd class="mt-1 font-semibold">{{ $opd->users_count }}</dd></div>
            <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs font-bold uppercase text-slate-500">Kendaraan</dt><dd class="mt-1 font-semibold">{{ $opd->kendaraan_count }}</dd></div>
        </dl>
    </section>
</x-layouts.app>
