<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $announcements = Announcement::with('author')
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('author_id', $user->id);
            })
            ->latest()->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['super-admin', 'admin', 'guru', 'staff', 'operator']);
        })->get();

        if ($users->isEmpty()) {
            $users = User::all();
        }

        return view('admin.announcements.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'nullable|exists:users,id',
            'type' => 'required|in:general,academic,event,urgent',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        $validated['author_id'] = $validated['author_id'] ?? auth()->id();
        $validated['is_published'] = $request->has('is_published');

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show($encodedId)
    {
        $id = decode_id($encodedId);
        $announcement = Announcement::with('author')->findOrFail($id);
        return redirect()->route('admin.announcements.edit', encode_id($announcement->id));
    }

    public function edit($encodedId)
    {
        $id = decode_id($encodedId);
        $announcement = Announcement::with('author')->findOrFail($id);
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['super-admin', 'admin', 'guru', 'staff', 'operator']);
        })->get();

        if ($users->isEmpty()) {
            $users = User::all();
        }

        return view('admin.announcements.edit', compact('announcement', 'users'));
    }

    public function update(Request $request, $encodedId)
    {
        $id = decode_id($encodedId);
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'nullable|exists:users,id',
            'type' => 'required|in:general,academic,event,urgent',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        $validated['author_id'] = $validated['author_id'] ?? auth()->id();
        $validated['is_published'] = $request->has('is_published');

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy($encodedId)
    {
        $id = decode_id($encodedId);
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
