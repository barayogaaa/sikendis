<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserOpdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $users = User::query()
            ->with('opd')
            ->where('role', User::ROLE_USER_OPD)
            ->when($search, function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('opd', fn ($opd) => $opd->where('nama', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'userOpd' => new User(['aktif' => true]),
            'opds' => Opd::aktif()->orderBy('nama')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['role'] = User::ROLE_USER_OPD;
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Akun User OPD berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $userOpd): View
    {
        $userOpd->load('opd')->loadCount('kendaraan');

        return view('admin.users.show', compact('userOpd'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $userOpd): View
    {
        return view('admin.users.edit', [
            'userOpd' => $userOpd,
            'opds' => Opd::aktif()->orderBy('nama')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $userOpd): RedirectResponse
    {
        $data = $this->validatedData($request, $userOpd);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['role'] = User::ROLE_USER_OPD;
        $userOpd->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Akun User OPD berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $userOpd): RedirectResponse
    {
        if ($userOpd->kendaraan()->exists()) {
            $userOpd->update(['aktif' => false]);

            return back()->with('success', 'Akun memiliki data kendaraan, sehingga dinonaktifkan.');
        }

        $userOpd->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun User OPD berhasil dihapus.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'opd_id' => ['required', 'exists:opds,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => $request->boolean('aktif')];
    }
}
