@csrf
@if ($opd->exists)
    @method('PUT')
@endif
<div class="grid gap-5 lg:grid-cols-2">
    <div class="lg:col-span-2">
        <label class="text-sm font-semibold text-slate-700">Nama OPD</label>
        <input name="nama" value="{{ old('nama', $opd->nama) }}" required class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Kode</label>
        <input name="kode" value="{{ old('kode', $opd->kode) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Telepon</label>
        <input name="telepon" value="{{ old('telepon', $opd->telepon) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div class="lg:col-span-2">
        <label class="text-sm font-semibold text-slate-700">Alamat</label>
        <input name="alamat" value="{{ old('alamat', $opd->alamat) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Penanggung Jawab</label>
        <input name="penanggung_jawab" value="{{ old('penanggung_jawab', $opd->penanggung_jawab) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <label class="flex items-center gap-2 self-end text-sm font-semibold text-slate-700">
        <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $opd->aktif ?? true)) class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
        OPD aktif
    </label>
</div>
<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('admin.opds.index') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-700 hover:bg-slate-100">Batal</a>
    <button class="inline-flex h-11 items-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white hover:bg-sky-700"><x-icon name="check" class="h-4 w-4"/> Simpan</button>
</div>
