<x-layouts.app heading="Edit User OPD">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('admin.users.update', $userOpd) }}">@include('admin.users._form')</form>
    </section>
</x-layouts.app>
