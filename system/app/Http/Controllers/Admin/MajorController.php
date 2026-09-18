<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $query = Major::withCount(['classes', 'students']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $majors = $query->latest()->paginate(15)->withQueryString();

        $totalMajors = Major::count();
        $vocationalCount = Major::where('type', 'vocational')->count();
        $academicCount = Major::where('type', 'academic')->count();

        return view('admin.majors.index', compact(
            'majors',
            'totalMajors',
            'vocationalCount',
            'academicCount'
        ));
    }

    public function create()
    {
        return view('admin.majors.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'slug' => 'nullable|string|max:255|unique:majors',
                'description' => 'nullable|string',
                'type' => 'required|in:academic,vocational',
            ]);

            $validated['code'] = $validated['code'] ?? strtoupper(Str::slug($validated['name'], '-'));
            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
            $validated['is_active'] = $request->has('is_active');

            // Ensure unique code
            $counter = 1;
            $originalCode = $validated['code'];
            while (Major::where('code', $validated['code'])->exists()) {
                $validated['code'] = $originalCode . '-' . $counter;
                $counter++;
            }

            Major::create($validated);

            return redirect()->route('admin.majors.index')
                ->with('success', 'Jurusan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['system' => 'Error: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Major $major)
    {
        return view('admin.majors.show', compact('major'));
    }

    public function edit(Major $major)
    {
        return view('admin.majors.edit', compact('major'));
    }

    public function update(Request $request, Major $major)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'slug' => 'nullable|string|max:255|unique:majors,slug,' . $major->id,
                'description' => 'nullable|string',
                'type' => 'required|in:academic,vocational',
            ]);

            $validated['code'] = $validated['code'] ?? strtoupper(Str::slug($validated['name'], '-'));
            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
            $validated['is_active'] = $request->has('is_active');

            // Ensure unique code (excluding current major)
            if ($validated['code'] !== $major->code) {
                $counter = 1;
                $originalCode = $validated['code'];
                while (Major::where('code', $validated['code'])->where('id', '!=', $major->id)->exists()) {
                    $validated['code'] = $originalCode . '-' . $counter;
                    $counter++;
                }
            }

            $major->update($validated);

            return redirect()->route('admin.majors.index')
                ->with('success', 'Jurusan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['system' => 'Error: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Major $major)
    {
        $major->delete();

        return redirect()->route('admin.majors.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
