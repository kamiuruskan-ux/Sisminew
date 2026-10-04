<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index()
    {
        $testimonials = Testimonial::ordered()->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Store a newly created testimonial in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'title' => 'nullable|string|max:150',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $savedPath = save_uploaded_public_file($request->file('avatar'), 'img/testimonials');
            $validated['avatar'] = $savedPath;
        }

        $validated['order'] = $validated['order'] ?? ((int) Testimonial::max('order') + 1);
        $validated['is_active'] = $request->has('is_active');

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni berhasil ditambahkan!');
    }

    /**
     * Update the specified testimonial in storage.
     */
    public function update(Request $request, $encodedId)
    {
        $id = decode_id($encodedId);
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'title' => 'nullable|string|max:150',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar) {
                delete_public_file($testimonial->avatar, 'img/testimonials');
            }
            $savedPath = save_uploaded_public_file($request->file('avatar'), 'img/testimonials');
            $validated['avatar'] = $savedPath;
        } else {
            unset($validated['avatar']);
        }

        $validated['order'] = $validated['order'] ?? $testimonial->order;
        $validated['is_active'] = $request->has('is_active');

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni berhasil diperbarui!');
    }

    /**
     * Remove the specified testimonial from storage.
     */
    public function destroy($encodedId)
    {
        $id = decode_id($encodedId);
        $testimonial = Testimonial::findOrFail($id);

        if ($testimonial->avatar) {
            delete_public_file($testimonial->avatar, 'img/testimonials');
        }

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni berhasil dihapus!');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($encodedId)
    {
        $id = decode_id($encodedId);
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Status testimoni berhasil diubah!');
    }
}
