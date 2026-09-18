<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExtracurricularController extends Controller
{
    public function index(Request $request)
    {
        $query = Extracurricular::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('schedule', 'like', "%{$search}%");
            });
        }

        $extracurriculars = $query->orderBy('order', 'asc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $categories = [
            'kepemimpinan' => 'Kepemimpinan & Organisasi',
            'sains' => 'Sains & Teknologi',
            'olahraga' => 'Olahraga & Atletik',
            'seni' => 'Seni & Budaya',
        ];

        return view('admin.extracurriculars.index', compact('extracurriculars', 'categories'));
    }

    public function create()
    {
        $categories = [
            'kepemimpinan' => 'Kepemimpinan & Organisasi',
            'sains' => 'Sains & Teknologi',
            'olahraga' => 'Olahraga & Atletik',
            'seni' => 'Seni & Budaya',
        ];

        return view('admin.extracurriculars.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'schedule' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg,jfif|max:10240',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        if ($request->hasFile('image')) {
            $savedPath = save_uploaded_public_file($request->file('image'), 'img/extracurriculars');
            $validated['image'] = basename($savedPath);
        }

        Extracurricular::create($validated);

        return redirect()->route('admin.extracurriculars.index')
            ->with('success', 'Kegiatan Ekstrakurikuler berhasil ditambahkan.');
    }

    public function edit(Extracurricular $extracurricular)
    {
        $categories = [
            'kepemimpinan' => 'Kepemimpinan & Organisasi',
            'sains' => 'Sains & Teknologi',
            'olahraga' => 'Olahraga & Atletik',
            'seni' => 'Seni & Budaya',
        ];

        return view('admin.extracurriculars.edit', compact('extracurricular', 'categories'));
    }

    public function update(Request $request, Extracurricular $extracurricular)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'schedule' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg,jfif|max:10240',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($extracurricular->image) {
                delete_public_file($extracurricular->image, 'img/extracurriculars');
            }
            $savedPath = save_uploaded_public_file($request->file('image'), 'img/extracurriculars');
            $validated['image'] = 'extracurriculars/' . basename($savedPath);
        } else {
            unset($validated['image']);
        }

        $extracurricular->update($validated);

        return redirect()->route('admin.extracurriculars.index')
            ->with('success', 'Data Ekstrakurikuler berhasil diperbarui.');
    }

    public function destroy(Extracurricular $extracurricular)
    {
        if ($extracurricular->image) {
            delete_public_file($extracurricular->image, 'img/extracurriculars');
        }

        $extracurricular->delete();

        return redirect()->route('admin.extracurriculars.index')
            ->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }

    public function toggleStatus(Extracurricular $extracurricular)
    {
        $extracurricular->update(['is_active' => !$extracurricular->is_active]);

        return redirect()->back()->with('success', 'Status ekstrakurikuler berhasil diubah.');
    }
}
