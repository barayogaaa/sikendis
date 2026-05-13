<x-layouts.app heading="Edit Data Import">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        @if ($referensi->kendaraan()->exists())
            <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Data ini sudah dipilih OPD. Perubahan di sini hanya memperbaiki data referensi import dan tidak otomatis mengubah data kendaraan yang sudah diinput OPD.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.import-referensi.update', $referensi) }}">
            @include('admin.import-referensi._form')
        </form>
    </section>
</x-layouts.app>
