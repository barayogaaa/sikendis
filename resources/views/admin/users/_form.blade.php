@csrf
@if ($userOpd->exists)
    @method('PUT')
@endif
<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-slate-700">Nama</label>
        <input name="name" value="{{ old('name', $userOpd->name) }}" required class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $userOpd->email) }}" required class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div class="lg:col-span-2">
        <label class="text-sm font-semibold text-slate-700">OPD</label>
        <select name="opd_id" required class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
            <option value="">Pilih OPD</option>
            @foreach ($opds as $opd)
                <option value="{{ $opd->id }}" @selected((int) old('opd_id', $userOpd->opd_id) === $opd->id)>{{ $opd->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Password</label>
        <input type="password" name="password" @required(! $userOpd->exists) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" @required(! $userOpd->exists) class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $userOpd->aktif ?? true)) class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
        Akun aktif
    </label>
</div>
<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('admin.users.index') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-700 hover:bg-slate-100">Batal</a>
    <button class="inline-flex h-11 items-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white hover:bg-sky-700"><x-icon name="check" class="h-4 w-4"/> Simpan</button>
</div>
