<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student || !$student->class_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        $materials = Material::where('class_id', $student->class_id)
            ->where('is_published', true)
            ->with(['teacher', 'class'])
            ->latest()
            ->paginate(20);

        return view('student.materials.index', compact('materials'));
    }

    public function download(Material $material)
    {
        $student = Auth::user()->student;
        
        if (!$student || !$student->class_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        // Check if material is for student's class and published
        if ($material->class_id !== $student->class_id || !$material->is_published) {
            return redirect()->route('student.materials.index')
                ->with('error', 'Materi tidak tersedia.');
        }

        if (!$material->file_path) {
            return back()->with('error', 'File tidak tersedia.');
        }

        if (\Illuminate\Support\Facades\File::exists(public_path($material->file_path))) {
            return response()->download(public_path($material->file_path));
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('doc/' . $material->file_path))) {
            return response()->download(public_path('doc/' . $material->file_path));
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('img/' . $material->file_path))) {
            return response()->download(public_path('img/' . $material->file_path));
        }

        if (Storage::disk('doc')->exists($material->file_path)) {
            return Storage::disk('doc')->download($material->file_path);
        }
        if (Storage::disk('public')->exists($material->file_path)) {
            return Storage::disk('public')->download($material->file_path);
        }

        return back()->with('error', 'File materi tidak ditemukan.');
    }
}
