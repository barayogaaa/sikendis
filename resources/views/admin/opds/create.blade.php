<x-layouts.app heading="Tambah OPD">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('admin.opds.store') }}">@include('admin.opds._form')</form>
    </section>
</x-layouts.app>
