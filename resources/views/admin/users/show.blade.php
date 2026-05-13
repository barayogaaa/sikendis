<x-layouts.app heading="Detail User OPD">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">{{ $userOpd->opd?->nama }}</p>
                <h2 class="text-2xl font-black">{{ $userOpd->name }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $userOpd->email }}</p>
            </div>
            <a href="{{ route('admin.users.edit', $userOpd) }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 px-3 text-sm font-bold hover:bg-slate-100"><x-icon name="edit" class="h-4 w-4"/> Edit</a>
        </div>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-slate-50 p-4"><span class="text-xs font-bold uppercase text-slate-500">Status</span><p class="mt-1 font-semibold">{{ $userOpd->aktif ? 'Aktif' : 'Nonaktif' }}</p></div>
            <div class="rounded-lg bg-slate-50 p-4"><span class="text-xs font-bold uppercase text-slate-500">Data Kendaraan Diinput</span><p class="mt-1 font-semibold">{{ $userOpd->kendaraan_count }}</p></div>
        </div>
    </section>
</x-layouts.app>
