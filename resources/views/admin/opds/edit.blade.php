<x-layouts.app heading="Edit OPD">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('admin.opds.update', $opd) }}">@include('admin.opds._form')</form>
    </section>
</x-layouts.app>
