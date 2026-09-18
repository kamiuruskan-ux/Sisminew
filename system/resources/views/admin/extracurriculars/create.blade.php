@extends('layouts.admin')

@section('title', 'Tambah Ekstrakurikuler')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Tambah Ekstrakurikuler Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Isi rincian informasi kegiatan ekstrakurikuler sekolah.</p>
        </div>
        <a href="{{ route('admin.extracurriculars.index') }}" class="btn-secondary">
            ← Kembali
        </a>
    </div>

    <div class="tailadmin-card p-6">
        <form action="{{ route('admin.extracurriculars.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nama Ekstrakurikuler *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700" placeholder="Contoh: Klub Robotik & AI">
                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kategori *</label>
                    <select name="category" required class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700">
                        @foreach($categories as $key => $catName)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $catName }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Jadwal Pelaksanaan</label>
                    <input type="text" name="schedule" value="{{ old('schedule') }}" class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700" placeholder="Contoh: Rabu (15:30 WIB)">
                    @error('schedule') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi Kegiatan</label>
                <textarea name="description" rows="3" class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700" placeholder="Jelaskan mengenai kegiatan ekstrakurikuler ini...">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Urutan Tampil</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700" placeholder="0">
                </div>

                <div class="flex items-center pt-6">
                    <label class="inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-primary border-slate-300">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Tampilkan di Website (Aktif)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.extracurriculars.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Ekstrakurikuler</button>
            </div>
        </form>
    </div>
</div>
@endsection
