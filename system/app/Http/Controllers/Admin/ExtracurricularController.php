<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use App\Models\ExtracurricularCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExtracurricularController extends Controller
{
    public function index(Request $request)
    {
        ExtracurricularCategory::ensureTableExists();

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

        $categories = ExtracurricularCategory::getCategoryList();
        $categoriesData = ExtracurricularCategory::orderBy('order')->orderBy('name')->get();

        return view('admin.extracurriculars.index', compact('extracurriculars', 'categories', 'categoriesData'));
    }

    public function create()
    {
        $categories = ExtracurricularCategory::getCategoryList();

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

    public function show($id = null)
    {
        return redirect()->route('admin.extracurriculars.index');
    }

    public function edit(Extracurricular $extracurricular)
    {
        $categories = ExtracurricularCategory::getCategoryList();

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

    /**
     * Manajemen Kategori Ekstrakurikuler
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:extracurricular_categories,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:30',
            'order' => 'nullable|integer',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        
        // Pastikan slug unik
        $baseSlug = $slug;
        $counter = 1;
        while (ExtracurricularCategory::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        ExtracurricularCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? 'emerald',
            'order' => $validated['order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.extracurriculars.index')
            ->with('success', 'Kategori ekstrakurikuler baru berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = ExtracurricularCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:30',
            'order' => 'nullable|integer',
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? $category->description,
            'color' => $validated['color'] ?? $category->color,
            'order' => $validated['order'] ?? $category->order,
        ]);

        return redirect()->route('admin.extracurriculars.index')
            ->with('success', 'Kategori ekstrakurikuler berhasil diperbarui.');
    }

    public function destroyCategory($id)
    {
        $category = ExtracurricularCategory::findOrFail($id);
        
        // Cek apakah kategori sedang digunakan
        $countInUse = Extracurricular::where('category', $category->slug)->count();
        if ($countInUse > 0) {
            return redirect()->route('admin.extracurriculars.index')
                ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$countInUse} ekstrakurikuler.");
        }

        $category->delete();

        return redirect()->route('admin.extracurriculars.index')
            ->with('success', 'Kategori ekstrakurikuler berhasil dihapus.');
    }
}
