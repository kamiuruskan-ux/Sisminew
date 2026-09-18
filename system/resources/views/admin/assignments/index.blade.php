@extends('layouts.admin')

@section('title', 'Tugas & PR Siswa')
@section('page_title', 'Manajemen Tugas & PR')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(encryptedId, title) {
        this.deleteTarget = { id: encryptedId, name: title };
        this.deleteFormAction = '{{ url('admin/assignments') }}/' + encryptedId;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Tugas', 'message' => 'Apakah Anda yakin ingin menghapus tugas :name ini? Data pengumpulan siswa terkait akan terhapus.'])


    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Tugas & Pekerjaan Rumah</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola penugasan pembelajaran, tenggat waktu (due date), dan pengumpulan tugas siswa.</p>
        </div>
        <div>
            <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Tugas Baru</span>
            </a>
        </div>
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penugasan</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($totalAssignments) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dipublikasikan (Aktif)</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($publishedCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Draft Disimpan</p>
                <p class="text-2xl font-bold text-amber-600 mt-1 tracking-tight">{{ number_format($draftCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ditutup / Selesai</p>
                <p class="text-2xl font-bold text-slate-600 mt-1 tracking-tight">{{ number_format($closedCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar (Real-Time Auto Submit) -->
    <div class="bg-white dark:bg-[#24303F] rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.assignments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Cari Tugas / Mapel</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Judul tugas atau mata pelajaran..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 pr-8 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    @if(request('search'))
                        <a href="{{ route('admin.assignments.index', request()->except('search')) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <x-major-class-select 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="inline" 
                major-label="Jurusan" 
                class-label="Kelas" 
                select-class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500" 
                label-class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" 
                :on-major-change="'$el.closest(\'form\').submit()'"
                :on-class-change="'$el.closest(\'form\').submit()'"
            />

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status Publikasi</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published (Dipublikasikan)</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed (Selesai/Ditutup)</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Penugasan Siswa</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} dari {{ $assignments->total() }} tugas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Judul Tugas & Deskripsi</th>
                        <th class="px-5 py-3">Mata Pelajaran</th>
                        <th class="px-5 py-3">Kelas Target</th>
                        <th class="px-5 py-3">Tenggat Waktu (Due Date)</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Submisi</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($assignments as $assignment)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Title & Description -->
                            <td class="px-5 py-3.5 max-w-xs">
                                <div class="flex items-start space-x-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs border border-indigo-100 shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-xs truncate">{{ $assignment->title }}</p>
                                        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ \Illuminate\Support\Str::limit(strip_tags($assignment->description), 60) }}</p>
                                        @if($assignment->topic)
                                            <div class="mt-1">
                                                <a href="{{ route('admin.lms.chapters.index') }}" class="inline-flex items-center space-x-1 text-[10px] bg-purple-50 text-purple-700 hover:bg-purple-100 font-semibold px-2 py-0.5 rounded border border-purple-200 transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                    <span>Sub-Bab LMS: {{ $assignment->topic->title }}</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Subject -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded font-semibold text-xs border border-slate-200">
                                    {{ $assignment->subject }}
                                </span>
                            </td>

                            <!-- Class -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded font-semibold text-xs border border-blue-200">
                                    {{ $assignment->class->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Due Date -->
                            <td class="px-5 py-3.5">
                                <div class="space-y-0.5">
                                    <p class="font-semibold text-slate-900 text-xs">
                                        {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, H:i') : '-' }}
                                    </p>
                                    @if($assignment->due_date && \Carbon\Carbon::parse($assignment->due_date)->isPast() && $assignment->status === 'published')
                                        <span class="inline-flex px-1.5 py-0.2 rounded bg-rose-50 text-rose-600 font-bold text-[9px] border border-rose-200">
                                            Tenggat Berakhir
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($assignment->status === 'published')
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">
                                        Published
                                    </span>
                                @elseif($assignment->status === 'draft')
                                    <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase">
                                        Draft
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">
                                        Closed
                                    </span>
                                @endif
                            </td>

                            <!-- Submissions -->
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-full font-bold text-xs border border-indigo-100">
                                    {{ $assignment->submissions->count() }} Submisi
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <!-- Detail -->
                                    <a href="{{ route('admin.assignments.show', \App\Helpers\IdEncrypter::encrypt($assignment->id)) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Detail & Submisi Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.assignments.edit', \App\Helpers\IdEncrypter::encrypt($assignment->id)) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Tugas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                     <!-- Delete -->
                                     <button type="button" @click="confirmDelete('{{ \App\Helpers\IdEncrypter::encrypt($assignment->id) }}', '{{ addslashes($assignment->title) }}')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Hapus Tugas">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                         </svg>
                                     </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Tidak ada data penugasan siswa yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
