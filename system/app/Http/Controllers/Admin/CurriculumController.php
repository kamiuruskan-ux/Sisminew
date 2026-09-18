<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumFeature;
use App\Support\Setting;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $features = CurriculumFeature::orderBy('order', 'asc')->orderBy('id', 'asc')->paginate(10);
        $curriculumDescription = Setting::get('curriculum_description', 'Mengintegrasikan Kurikulum Merdeka Belajar dengan penguatan karakter Pancasila dan literasi teknologi global.');
        $curriculumTitle = Setting::get('curriculum_title', 'Kurikulum & Program Pendidikan');

        return view('admin.curriculum.index', compact('features', 'curriculumDescription', 'curriculumTitle'));
    }

    public function updateDescription(Request $request)
    {
        $request->validate([
            'curriculum_title' => 'required|string|max:255',
            'curriculum_description' => 'required|string',
        ]);

        Setting::set('curriculum_title', $request->curriculum_title);
        Setting::set('curriculum_description', $request->curriculum_description);

        return redirect()->route('admin.curriculum.index')
            ->with('success', 'Deskripsi umum Kurikulum berhasil diperbarui.');
    }

    public function create()
    {
        return view('admin.curriculum.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|in:blue,purple,emerald,amber,rose,indigo,cyan',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        CurriculumFeature::create($validated);

        return redirect()->route('admin.curriculum.index')
            ->with('success', 'Pilar Kurikulum berhasil ditambahkan.');
    }

    public function edit(CurriculumFeature $curriculum)
    {
        return view('admin.curriculum.edit', compact('curriculum'));
    }

    public function update(Request $request, CurriculumFeature $curriculum)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|in:blue,purple,emerald,amber,rose,indigo,cyan',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        $curriculum->update($validated);

        return redirect()->route('admin.curriculum.index')
            ->with('success', 'Pilar Kurikulum berhasil diperbarui.');
    }

    public function destroy(CurriculumFeature $curriculum)
    {
        $curriculum->delete();

        return redirect()->route('admin.curriculum.index')
            ->with('success', 'Pilar Kurikulum berhasil dihapus.');
    }

    public function toggleStatus(CurriculumFeature $curriculum)
    {
        $curriculum->update(['is_active' => !$curriculum->is_active]);

        return redirect()->back()->with('success', 'Status pilar kurikulum berhasil diubah.');
    }
}
