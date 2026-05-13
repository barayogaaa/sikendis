<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $opds = Opd::query()
            ->withCount(['users', 'kendaraan'])
            ->when($search, function ($query) use ($search): void {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.opds.index', compact('opds', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.opds.create', ['opd' => new Opd]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Opd::create($this->validatedData($request));

        return redirect()->route('admin.opds.index')->with('success', 'Data OPD berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Opd $opd): View
    {
        $opd->loadCount(['users', 'kendaraan']);

        return view('admin.opds.show', compact('opd'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Opd $opd): View
    {
        return view('admin.opds.edit', compact('opd'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Opd $opd): RedirectResponse
    {
        $opd->update($this->validatedData($request, $opd));

        return redirect()->route('admin.opds.index')->with('success', 'Data OPD berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Opd $opd): RedirectResponse
    {
        if ($opd->kendaraan()->exists() || $opd->users()->exists()) {
            return back()->with('error', 'OPD tidak dapat dihapus karena sudah memiliki user atau kendaraan.');
        }

        $opd->delete();

        return redirect()->route('admin.opds.index')->with('success', 'Data OPD berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Opd $opd = null): array
    {
        $id = $opd?->id ?? 'NULL';

        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['nullable', 'string', 'max:50', 'unique:opds,kode,'.$id],
            'alamat' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => $request->boolean('aktif')];
    }
}
