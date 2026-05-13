<x-layouts.app heading="Data OPD">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="relative w-full sm:max-w-md">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
            <input name="search" value="{{ $search }}" placeholder="Cari nama, kode, penanggung jawab" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </form>
        <a href="{{ route('admin.opds.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white transition hover:bg-sky-700">
            <x-icon name="plus" class="h-4 w-4"/> Tambah OPD
        </a>
    </div>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3">OPD</th><th class="px-5 py-3">Kontak</th><th class="px-5 py-3">User</th><th class="px-5 py-3">Kendaraan</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($opds as $opd)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3"><b>{{ $opd->nama }}</b><span class="block text-xs text-slate-500">{{ $opd->kode ?: '-' }}</span></td>
                            <td class="px-5 py-3">{{ $opd->penanggung_jawab ?: '-' }}<span class="block text-xs text-slate-500">{{ $opd->telepon ?: '-' }}</span></td>
                            <td class="px-5 py-3">{{ $opd->users_count }}</td>
                            <td class="px-5 py-3">{{ $opd->kendaraan_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.opds.show', $opd) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 hover:bg-slate-50"><x-icon name="eye" class="h-4 w-4"/></a>
                                    <a href="{{ route('admin.opds.edit', $opd) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 hover:bg-slate-50"><x-icon name="edit" class="h-4 w-4"/></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada OPD.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $opds->links() }}</div>
    </section>
</x-layouts.app>
