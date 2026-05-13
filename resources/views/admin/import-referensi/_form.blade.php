@csrf
@if ($referensi->exists)
    @method('PUT')
@endif

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-slate-700">Plat Nomor</label>
        <input name="plat_nomor" value="{{ old('plat_nomor', $referensi->plat_nomor) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Merk</label>
        <input name="merk" value="{{ old('merk', $referensi->merk) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Tipe</label>
        <input name="tipe" value="{{ old('tipe', $referensi->tipe) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Tahun</label>
        <input type="number" name="tahun" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('tahun', $referensi->tahun) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nomor Rangka</label>
        <input name="nomor_rangka" value="{{ old('nomor_rangka', $referensi->nomor_rangka) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nomor Mesin</label>
        <input name="nomor_mesin" value="{{ old('nomor_mesin', $referensi->nomor_mesin) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
    <div class="lg:col-span-2">
        <label class="text-sm font-semibold text-slate-700">Nomor BPKB</label>
        <input name="nomor_bpkb" value="{{ old('nomor_bpkb', $referensi->nomor_bpkb) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.import-referensi.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-100">Batal</a>
    <button class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white transition hover:bg-sky-700">
        <x-icon name="check" class="h-4 w-4"/>
        Simpan
    </button>
</div>
