<x-layouts.app heading="Input Kendaraan">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('kendaraans.store') }}" enctype="multipart/form-data">
            @include('kendaraans._form')
        </form>
    </section>
</x-layouts.app>
