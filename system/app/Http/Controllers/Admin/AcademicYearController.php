<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::latest()->get();
        $activeYear = AcademicYear::where('is_active', true)->first();
        return view('admin.academic-years.index', compact('academicYears', 'activeYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years',
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|min:2000|max:2100|gt:start_year',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'semester' => 'required|in:ganjil,genap,1,2',
            'is_active' => 'boolean',
        ]);

        if ($validated['semester'] == '1') $validated['semester'] = 'ganjil';
        if ($validated['semester'] == '2') $validated['semester'] = 'genap';

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        // If active, deactivate others
        if ($validated['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($validated);

        return back()->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name,' . $academicYear->id,
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|min:2000|max:2100|gt:start_year',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'semester' => 'required|in:ganjil,genap,1,2',
            'is_active' => 'boolean',
        ]);

        if ($validated['semester'] == '1') $validated['semester'] = 'ganjil';
        if ($validated['semester'] == '2') $validated['semester'] = 'genap';

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        // If active, deactivate others (except current)
        if ($validated['is_active']) {
            AcademicYear::where('is_active', true)
                ->where('id', '!=', $academicYear->id)
                ->update(['is_active' => false]);
        }

        $academicYear->update($validated);

        return back()->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        // Check if has related waves
        if ($academicYear->waves()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus tahun akademik yang masih memiliki gelombang pendaftaran.');
        }

        $academicYear->delete();

        return back()->with('success', 'Tahun akademik berhasil dihapus.');
    }

    public function setActive(AcademicYear $academicYear)
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return back()->with('success', 'Tahun akademik aktif berhasil diubah.');
    }
}
