<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wave;
use Illuminate\Http\Request;

class WaveController extends Controller
{
    public function index()
    {
        $waves = Wave::latest()->paginate(15);
        return view('admin.waves.index', compact('waves'));
    }

    public function create()
    {
        return view('admin.waves.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,draft,closed',
            'quota' => 'nullable|integer',
            'spp_discount' => 'nullable|numeric|min:0',
        ]);

        Wave::create($validated);

        return redirect()->route('admin.waves.index')
            ->with('success', 'Gelombang pendaftaran berhasil ditambahkan.');
    }

    public function show(Wave $wave)
    {
        return view('admin.waves.show', compact('wave'));
    }

    public function edit(Wave $wave)
    {
        return view('admin.waves.edit', compact('wave'));
    }

    public function update(Request $request, Wave $wave)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,draft,closed',
            'quota' => 'nullable|integer',
            'spp_discount' => 'nullable|numeric|min:0',
        ]);

        $wave->update($validated);

        return redirect()->route('admin.waves.index')
            ->with('success', 'Gelombang pendaftaran berhasil diperbarui.');
    }

    public function destroy(Wave $wave)
    {
        $wave->delete();

        return redirect()->route('admin.waves.index')
            ->with('success', 'Gelombang pendaftaran berhasil dihapus.');
    }
}
