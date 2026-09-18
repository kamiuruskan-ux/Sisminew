@extends('layouts.admin')

@section('title', 'Tambah Pengumuman')
@section('page_title', 'Tambah Pengumuman Baru')

@section('content')
<div class="w-full space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center space-x-5">
            <a href="{{ route('admin.announcements.index') }}" class="w-12 h-12 flex items-center justify-center bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 hover:shadow-lg transition-all rounded-2xl group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Buat Pengumuman</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 italic">Drafting informasi publik sekolah</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button type="submit" form="announcementForm" class="btn-primary flex items-center space-x-2 px-8 py-3.5 text-xs uppercase tracking-[0.2em] font-black transition-all hover:scale-105 active:scale-95 shadow-xl shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
                <span>Publikasikan Sekarang</span>
            </button>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="premium-card bg-rose-50 border-rose-100 p-6">
            <div class="flex items-center space-x-3 text-rose-600 mb-4">
                <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest">Validasi Gagal</h3>
            </div>
            <ul class="space-y-1 ml-13">
                @foreach($errors->all() as $error)
                    <li class="text-xs font-bold text-rose-500 uppercase tracking-wide">• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Form -->
    <form id="announcementForm" action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content - 2/3 width -->
            <div class="lg:col-span-2 space-y-8">
                <div class="premium-card overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30">
                        <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em] flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editor Konten
                        </h3>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 px-1">Judul Pengumuman <span class="text-rose-500">*</span></label>
                            <input type="text"
                                   name="title"
                                   value="{{ old('title') }}"
                                   class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all text-lg font-black text-slate-900 placeholder:text-slate-300"
                                   placeholder="Masukkan judul pengumuman yang menarik..."
                                   required>
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 px-1">Isi Pengumuman <span class="text-rose-500">*</span></label>
                            <div class="rounded-2xl overflow-hidden border border-slate-100">
                                <textarea name="content"
                                          id="content"
                                          rows="15"
                                          class="w-full px-4 py-3 border-none focus:ring-0 transition summernote"
                                          required>{{ old('content') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - 1/3 width -->
            <div class="space-y-8">
                <!-- Publish Settings Card -->
                <div class="premium-card overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-50 bg-slate-50/30">
                        <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em] flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Penjadwalan
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Tanggal Publish</label>
                            <input type="datetime-local"
                                   name="published_at"
                                   value="{{ old('published_at') }}"
                                   class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all text-sm font-bold text-slate-900">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2 px-1">● Kosongkan untuk publish instan</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Batas Waktu</label>
                            <input type="datetime-local"
                                   name="expires_at"
                                   value="{{ old('expires_at') }}"
                                   class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all text-sm font-bold text-slate-900">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2 px-1 italic">● Auto-hide setelah waktu habis</p>
                        </div>
                    </div>
                </div>

                <!-- Type & Status Card -->
                <div class="premium-card overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-50 bg-slate-50/30">
                        <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em] flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Kategori & Status
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Tipe Informasi</label>
                            <div class="relative">
                                <select name="type" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all text-sm font-bold text-slate-900 appearance-none">
                                    <option value="general" {{ old('type', 'general') == 'general' ? 'selected' : '' }}>Informasi Umum</option>
                                    <option value="academic" {{ old('type', 'academic') == 'academic' ? 'selected' : '' }}>Akademik</option>
                                    <option value="event" {{ old('type', 'event') == 'event' ? 'selected' : '' }}>Kegiatan/Acara</option>
                                    <option value="urgent" {{ old('type', 'urgent') == 'urgent' ? 'selected' : '' }}>Penting (Urgent)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="relative flex items-center justify-between p-4 bg-slate-50 rounded-2xl cursor-pointer group hover:bg-slate-100 transition-all border border-transparent hover:border-indigo-100">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <input type="checkbox"
                                               name="is_published"
                                               value="1"
                                               {{ old('is_published', true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Publikasikan</span>
                                </div>
                                <svg class="w-5 h-5 text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        overflow: hidden;
        box-shadow: none !important;
    }
    .note-toolbar {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 8px 12px !important;
    }
    .note-btn {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        padding: 5px 10px !important;
        font-size: 12px !important;
        color: #334155 !important;
    }
    .note-btn:hover {
        background-color: #f1f5f9 !important;
        color: #4f46e5 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('#content').summernote({
        placeholder: 'Tuliskan isi pengumuman lengkap di sini...',
        tabsize: 2,
        height: 450,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                $('#content').val(contents);
            }
        }
    });
});
</script>
@endpush
@endsection
