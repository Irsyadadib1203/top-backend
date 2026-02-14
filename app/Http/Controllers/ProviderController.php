<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::latest()->get();
        return view('admin.provider.provider', compact('providers'));
    }

    public function create()
    {
        return view('admin.provider.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:providers,code',
            'api_url' => 'nullable|string',
            'api_key' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        Provider::create($request->all());

        return redirect()
            ->route('provider.index')
            ->with('success', 'Provider berhasil ditambahkan');
    }

    public function edit(Provider $provider)
    {
        return view('admin.provider.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:providers,code,' . $provider->id,
            'api_url' => 'nullable|string',
            'api_key' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $provider->update($request->all());

        return redirect()
            ->route('provider.index')
            ->with('success', 'Provider berhasil diupdate');
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();

        return back()->with('success', 'Provider berhasil dihapus');
    }
}
