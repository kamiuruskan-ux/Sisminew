<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpmbFormField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpmbFormFieldController extends Controller
{
    /**
     * Display listing of SPMB custom form fields.
     */
    public function index()
    {
        $fields = SpmbFormField::ordered()->get();
        $fieldsBySection = $fields->groupBy('section');

        $sections = [
            'student' => 'Data Pribadi Calon Siswa',
            'parent' => 'Data Orang Tua / Wali',
            'religious' => 'Keagamaan & Al-Qur\'an',
            'health' => 'Kesehatan & Karakteristik',
            'other' => 'Kuesioner / Informasi Tambahan',
        ];

        return view('admin.spmb.fields.index', compact('fields', 'fieldsBySection', 'sections'));
    }

    /**
     * Store a newly created form field.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'section' => 'required|in:student,parent,religious,health,other',
            'type' => 'required|in:text,textarea,number,select,radio,checkbox,date',
            'options_text' => 'nullable|string',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);

        // Generate clean field_key slug
        $baseKey = Str::slug($validated['label'], '_');
        if (empty($baseKey)) {
            $baseKey = 'field_' . time();
        }
        $key = $baseKey;
        $counter = 1;
        while (SpmbFormField::where('field_key', $key)->exists()) {
            $key = $baseKey . '_' . $counter++;
        }
        $validated['field_key'] = $key;

        // Process options
        $options = null;
        if (in_array($validated['type'], ['select', 'radio', 'checkbox']) && !empty($validated['options_text'])) {
            $options = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r|,/", $validated['options_text']))));
        }
        $validated['options'] = $options;

        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? ((int) SpmbFormField::where('section', $validated['section'])->max('order') + 1);

        SpmbFormField::create($validated);

        return redirect()->route('admin.spmb.fields.index')
            ->with('success', 'Pertanyaan formulir berhasil ditambahkan!');
    }

    /**
     * Update the specified form field.
     */
    public function update(Request $request, $encodedId)
    {
        $id = is_numeric($encodedId) ? (int) $encodedId : decode_id($encodedId);
        $field = SpmbFormField::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'section' => 'required|in:student,parent,religious,health,other',
            'type' => 'required|in:text,textarea,number,select,radio,checkbox,date',
            'options_text' => 'nullable|string',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);

        // Process options
        $options = null;
        if (in_array($validated['type'], ['select', 'radio', 'checkbox']) && !empty($validated['options_text'])) {
            $options = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r|,/", $validated['options_text']))));
        }
        $validated['options'] = $options;

        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? $field->order;

        $field->update($validated);

        return redirect()->route('admin.spmb.fields.index')
            ->with('success', 'Pertanyaan formulir berhasil diperbarui!');
    }

    /**
     * Delete the specified form field.
     */
    public function destroy($encodedId)
    {
        $id = is_numeric($encodedId) ? (int) $encodedId : decode_id($encodedId);
        $field = SpmbFormField::findOrFail($id);
        $field->delete();

        return redirect()->route('admin.spmb.fields.index')
            ->with('success', 'Pertanyaan formulir berhasil dihapus!');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($encodedId)
    {
        $id = is_numeric($encodedId) ? (int) $encodedId : decode_id($encodedId);
        $field = SpmbFormField::findOrFail($id);
        $field->update(['is_active' => !$field->is_active]);

        return redirect()->route('admin.spmb.fields.index')
            ->with('success', 'Status pertanyaan formulir berhasil diubah!');
    }
}
