<x-layouts.app heading="User OPD">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="relative w-full sm:max-w-md">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"/>
            <input name="search" value="{{ $search }}" placeholder="Cari nama, email, OPD" class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </form>
        <a href="{{ route('admin.users.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white transition hover:bg-sky-700"><x-icon name="plus" class="h-4 w-4"/> Tambah User</a>
    </div>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">OPD</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $userOpd)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-bold">{{ $userOpd->name }}</td>
                            <td class="px-5 py-3">{{ $userOpd->email }}</td>
                            <td class="px-5 py-3">{{ $userOpd->opd?->nama }}</td>
                            <td class="px-5 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $userOpd->aktif ? 'bg-emerald-100 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ $userOpd->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $userOpd) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 hover:bg-slate-50"><x-icon name="eye" class="h-4 w-4"/></a>
                                    <a href="{{ route('admin.users.edit', $userOpd) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 hover:bg-slate-50"><x-icon name="edit" class="h-4 w-4"/></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada User OPD.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
    </section>
</x-layouts.app>
