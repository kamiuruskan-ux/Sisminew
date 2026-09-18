@extends('layouts.admin')

@section('title', 'Edit Pilar Kurikulum')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Edit Pilar Kurikulum</h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui rincian pilar {{ $curriculum->title }}.</p>
        </div>
        <a href="{{ route('admin.curriculum.index') }}" class="btn-secondary">
            ← Kembali
        </a>
    </div>

    <div class="tailadmin-card p-6">
        <form action="{{ route('admin.curriculum.update', $curriculum->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Judul Pilar *</label>
                <input type="text" name="title" value="{{ old('title', $curriculum->title) }}" required class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700">
                @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi Pilar</label>
                <textarea name="description" rows="3" class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700">{{ old('description', $curriculum->description) }}</textarea>
                @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Warna Aksen *</label>
                    <select name="color" required class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700">
                        <option value="blue" {{ old('color', $curriculum->color) == 'blue' ? 'selected' : '' }}>Biru (Blue)</option>
                        <option value="purple" {{ old('color', $curriculum->color) == 'purple' ? 'selected' : '' }}>Ungu (Purple)</option>
                        <option value="emerald" {{ old('color', $curriculum->color) == 'emerald' ? 'selected' : '' }}>Hijau (Emerald)</option>
                        <option value="amber" {{ old('color', $curriculum->color) == 'amber' ? 'selected' : '' }}>Kuning/Oranye (Amber)</option>
                        <option value="rose" {{ old('color', $curriculum->color) == 'rose' ? 'selected' : '' }}>Merah (Rose)</option>
                        <option value="indigo" {{ old('color', $curriculum->color) == 'indigo' ? 'selected' : '' }}>Nila (Indigo)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Urutan Tampil</label>
                    <input type="number" name="order" value="{{ old('order', $curriculum->order) }}" class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700">
                </div>
            </div>

            <div class="flex items-center pt-2">
                <label class="inline-flex items-center cursor-pointer gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $curriculum->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-primary border-slate-300">
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Tampilkan di Website (Aktif)</span>
                </label>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.curriculum.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Perbarui Pilar Kurikulum</button>
            </div>
        </form>
    </div>
</div>
@endsection
