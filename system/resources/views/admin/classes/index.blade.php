@extends('layouts.admin')

@section('title', 'Data Kelas')
@section('page_title', 'Manajemen Kelas')

@section('content')
<div class="space-y-6" x-data="classData()" x-init="initClassData()">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Ruang Kelas</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data kelas, jenjang tingkat, jurusan, serta kapasitas siswa.</p>
        </div>
        @permission('create-classes')
        <div>
            <button type="button" @click="openModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Kelas Baru</span>
            </button>
        </div>
        @endpermission
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Ruang Kelas</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($totalClasses) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas Aktif</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($activeClasses) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Siswa Terdaftar</p>
                <p class="text-2xl font-bold text-blue-600 mt-1 tracking-tight">{{ number_format($totalStudentsInClasses) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar (Real-Time Auto Submit) -->
    <div class="bg-white dark:bg-[#24303F] rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.classes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 {{ \App\Models\Setting::get('is_vocational', '1') == '1' ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-3">
            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Cari Kelas</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama kelas..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 pr-8 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    @if(request('search'))
                        <a href="{{ route('admin.classes.index', request()->except('search')) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Filter Level -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Jenjang / Tingkat</label>
                <select name="level" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Jenjang</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ request('level') == $i ? 'selected' : '' }}>Tingkat {{ $i }}</option>
                    @endfor
                </select>
            </div>

            @if(\App\Models\Setting::get('is_vocational', '1') == '1')
            <!-- Filter Major -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Jurusan</label>
                <select name="major_id" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Jurusan</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}" {{ request('major_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Ruang Kelas</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $classes->firstItem() ?? 0 }} - {{ $classes->lastItem() ?? 0 }} dari {{ $classes->total() }} kelas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Nama Kelas & Kode</th>
                        <th class="px-5 py-3">Jenjang Tingkat</th>
                        @if(\App\Models\Setting::get('is_vocational', '1') == '1')
                        <th class="px-5 py-3">Jurusan</th>
                        @endif
                        <th class="px-5 py-3">Wali Kelas</th>
                        <th class="px-5 py-3 text-center">Jumlah Siswa / Kapasitas</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($classes as $class)
                        @php
                            $studentsCount = $class->students_count ?? 0;
                            $capacity = $class->capacity ?? 0;
                            $percent = $capacity > 0 ? min(100, round(($studentsCount / $capacity) * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Class Name & Code -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs border border-indigo-100 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-xs">{{ $class->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">SLUG: {{ $class->slug }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Jenjang / Grade -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded font-semibold text-xs border border-slate-200">
                                    Tingkat {{ $class->grade ?? $class->level }}
                                </span>
                            </td>

                            <!-- Major -->
                            @if(\App\Models\Setting::get('is_vocational', '1') == '1')
                            <td class="px-5 py-3.5">
                                @if($class->major)
                                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded font-semibold text-xs border border-indigo-200">
                                        {{ $class->major->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Umum (Tanpa Jurusan)</span>
                                @endif
                            </td>
                            @endif

                            <!-- Wali Kelas -->
                            <td class="px-5 py-3.5">
                                @if($class->homeroomTeacher)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 font-extrabold flex items-center justify-center text-[10px]">
                                            {{ strtoupper(substr($class->homeroomTeacher->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-xs">{{ $class->homeroomTeacher->name }}</p>
                                            @if($class->homeroomTeacher->nip)
                                                <p class="text-[10px] text-slate-400 font-mono">NIP: {{ $class->homeroomTeacher->nip }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Belum Ditentukan</span>
                                @endif
                            </td>

                            <!-- Student Capacity & Progress -->
                            <td class="px-5 py-3.5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="font-bold text-slate-900 text-xs">
                                        {{ number_format($studentsCount) }} / {{ number_format($capacity) }} Siswa
                                    </span>
                                    <div class="w-24 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden border border-slate-200">
                                        <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($class->is_active)
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                             <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @permission('edit-classes')
                                    <button type="button" @click="openModal(true, {{ $class->id }})"
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Kelas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endpermission

                                    @permission('delete-classes')
                                    <button type="button" @click="confirmDelete({{ $class->id }}, '{{ addslashes($class->name) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors" title="Hapus Kelas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Tidak ada data ruang kelas yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $classes->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Class Form Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="showModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-slate-200 relative" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                <h3 class="font-bold text-slate-900 text-sm" x-text="editMode ? 'Edit Ruang Kelas' : 'Tambah Ruang Kelas Baru'"></h3>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="editMode ? '{{ route('admin.classes.update', ':id') }}'.replace(':id', formData.id) : '{{ route('admin.classes.store') }}'" method="POST" class="p-6 space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                @if($errors->any())
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-700 space-y-1">
                        @foreach($errors->all() as $err)
                            <p>• {{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                @if(\App\Models\Setting::get('is_vocational', '1') == '1')
                <!-- Jurusan (Utama) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jurusan</label>
                    <select name="major_id" x-model="formData.major_id" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="">Umum / Tanpa Jurusan</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}">{{ $major->name }}</option>
                        @endforeach
                    </select>
                    @error('major_id') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>
                @endif

                <!-- Nama Kelas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="formData.name" placeholder="Contoh: X TKR 1, XI TKJ 2" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    @error('name') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Jenjang -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenjang <span class="text-rose-500">*</span></label>
                        <select name="level" x-model="formData.level" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            <option value="">Pilih Jenjang</option>
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}">Tingkat {{ $i }}</option>
                            @endfor
                        </select>
                        @error('level') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kapasitas -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kapasitas (Siswa)</label>
                        <input type="number" name="capacity" x-model="formData.capacity" placeholder="36" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        @error('capacity') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Wali Kelas (Searchable Component) -->
                <div>
                    <x-teacher-select-search 
                        :teachers="$teachers" 
                        name="homeroom_teacher_id" 
                        value-type="id"
                        label="Wali Kelas" 
                        placeholder="-- Cari & Pilih Wali Kelas --" 
                    />
                    @error('homeroom_teacher_id') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Status Checkbox -->
                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" id="is_active_chk" x-model="formData.is_active" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active_chk" class="text-xs font-semibold text-slate-700 cursor-pointer">Kelas Aktif</label>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-xl border border-slate-200 text-center" @click.away="showDeleteModal = false">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Hapus Data Kelas?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus kelas <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex space-x-2">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition">
                    Batal
                </button>
                <form :action="deleteFormAction" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function classData() {
    return {
        showModal: {{ $errors->any() ? 'true' : 'false' }},
        showDeleteModal: false,
        editMode: false,
        deleteTarget: null,
        deleteFormAction: '',
        formData: { id: null, name: '', level: '', major_id: '', homeroom_teacher_id: '', capacity: '', is_active: true },
        openModal(edit = false, classId = null) {
            this.editMode = edit;
            if (edit && classId) {
                const classData = window.classDataStore[classId];
                this.formData = {
                    id: classData.id,
                    name: classData.name,
                    level: String(classData.grade || classData.level || ''),
                    major_id: classData.major_id || '',
                    homeroom_teacher_id: classData.homeroom_teacher_id || '',
                    capacity: classData.capacity || '',
                    is_active: !!classData.is_active
                };
            } else {
                this.formData = { id: null, name: '', level: '', major_id: '', homeroom_teacher_id: '', capacity: '', is_active: true };
            }
            this.showModal = true;
        },
        confirmDelete(classId, className) {
            this.deleteTarget = { id: classId, name: className };
            this.deleteFormAction = '{{ url('admin/classes') }}/' + classId;
            this.showDeleteModal = true;
        }
    }
}

function initClassData() {
    window.classDataStore = @json($classes->getCollection()->mapWithKeys(fn($c) => [$c->id => $c]));
}
</script>
@endsection
