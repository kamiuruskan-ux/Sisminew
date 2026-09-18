@extends('layouts.admin')

@section('title', 'Manajemen Materi')
@section('page_title', 'Materi Pembelajaran')

@section('content')
<div class="space-y-5" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, title) {
        this.deleteTarget = { id: id, name: title };
        this.deleteFormAction = '{{ url('admin/materials') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Materi Pembelajaran', 'message' => 'Apakah Anda yakin ingin menghapus materi :name ini? Tindakan ini tidak dapat dibatalkan.'])


    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Manajemen Materi</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola konten pembelajaran digital untuk siswa</p>
        </div>
        <a href="{{ route('admin.materials.create') }}"
           class="self-start sm:self-auto inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-xs font-black uppercase tracking-wider hover:brightness-110 shadow-sm transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Materi
        </a>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $stats = [
                ['label' => 'Total Materi',  'value' => $materials->total(),                              'color' => 'blue',   'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['label' => 'Dipublikasi',   'value' => $materials->where('is_published', 1)->count(),    'color' => 'emerald','icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Draft',         'value' => $materials->where('is_published', 0)->count(),    'color' => 'amber',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Ada Berkas',    'value' => $materials->whereNotNull('file_path')->count(),   'color' => 'purple', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ];
            $colorMap = [
                'blue'   => 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400',
                'emerald'=> 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400',
                'amber'  => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400',
                'purple' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400',
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 {{ $colorMap[$stat['color']] }} rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate">{{ $stat['label'] }}</p>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ $stat['value'] }}</h3>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filters (Real-Time Auto Submit) --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
        <form action="{{ route('admin.materials.index') }}" method="GET"
              class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari judul materi..."
                       @input.debounce.400ms="$el.closest('form').submit()"
                       x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                       class="w-full pl-9 pr-8 py-2.5 border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                @if(request('search'))
                    <a href="{{ route('admin.materials.index', request()->except('search')) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                @endif
            </div>
            <x-major-class-select 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="inline" 
                major-label="" 
                class-label="" 
                select-class="px-3 py-2.5 border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition" 
                :on-major-change="'$el.closest(\'form\').submit()'" 
                :on-class-change="'$el.closest(\'form\').submit()'" 
            />
        </form>
    </div>

    {{-- Tabel: desktop --}}
    <div class="hidden sm:block bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Materi & Mapel</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Target Kelas</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Diupdate</th>
                        <th class="px-4 py-3 text-right font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($materials as $material)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center shrink-0 text-slate-400 dark:text-slate-500">
                                        @if($material->file_path)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 dark:text-white truncate max-w-[200px]">{{ $material->title }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $material->subject }}</p>
                                        @if($material->topic)
                                            <div class="mt-1">
                                                <a href="{{ route('admin.lms.chapters.index') }}" class="inline-flex items-center space-x-1 text-[10px] bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 font-semibold px-2 py-0.5 rounded border border-purple-200 dark:border-purple-800 transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                    <span>Sub-Bab LMS: {{ $material->topic->title }}</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 rounded-md text-[10px] font-extrabold">
                                    {{ $material->class->name ?? 'Semua Kelas' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($material->is_published)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $material->updated_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($material->file_path)
                                        <a href="{{ route('admin.materials.download', $material) }}" class="p-1.5 text-slate-400 hover:bg-indigo-600 hover:text-white rounded-lg transition" title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.materials.edit', $material) }}" class="p-1.5 text-slate-400 hover:bg-blue-600 hover:text-white rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                     <button type="button" @click="confirmDelete({{ $material->id }}, '{{ addslashes($material->title) }}')" class="p-1.5 text-slate-400 hover:bg-rose-600 hover:text-white rounded-lg transition cursor-pointer" title="Hapus">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                     </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada data materi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->hasPages())
            <div class="px-5 py-4 bg-slate-50/70 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
                {{ $materials->links() }}
            </div>
        @endif
    </div>

    {{-- Card list: mobile --}}
    <div class="sm:hidden space-y-2.5">
        @forelse($materials as $material)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center shrink-0 text-slate-400 dark:text-slate-500">
                        @if($material->file_path)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-black text-slate-900 dark:text-white text-sm leading-tight">{{ $material->title }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $material->subject }}</p>
                    </div>
                    @if($material->is_published)
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">Published</span>
                    @else
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">Draft</span>
                    @endif
                </div>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 rounded-md text-[10px] font-extrabold">{{ $material->class->name ?? 'Semua Kelas' }}</span>
                    <span class="text-[10px] text-slate-400">{{ $material->updated_at->format('d M Y') }}</span>
                </div>
                <div class="flex items-center gap-2 pt-2.5 border-t border-slate-100 dark:border-slate-800">
                    @if($material->file_path)
                        <a href="{{ route('admin.materials.download', $material) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold hover:bg-indigo-100 hover:text-indigo-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh
                        </a>
                    @endif
                    <a href="{{ route('admin.materials.edit', $material) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold hover:bg-blue-100 hover:text-blue-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <button type="button" @click="confirmDelete({{ $material->id }}, '{{ addslashes($material->title) }}')" class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold hover:bg-rose-100 hover:text-rose-700 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>

                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 text-center">
                <p class="text-slate-400 text-sm">Belum ada data materi.</p>
            </div>
        @endforelse

        @if($materials->hasPages())
            <div class="pt-2">{{ $materials->links() }}</div>
        @endif
    </div>

</div>
@endsection
