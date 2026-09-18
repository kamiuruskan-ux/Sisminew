@extends('layouts.admin')

@section('title', 'Tahun Akademik')
@section('page_title', 'Tahun Akademik & Semester')

@section('content')
<div class="space-y-6" x-data="academicYearData()" x-init="initAcademicYearData()">
    @include('components.delete-modal', ['title' => 'Hapus Tahun Akademik', 'message' => 'Apakah Anda yakin ingin menghapus tahun akademik <strong x-text=\'deleteTarget?.name\'></strong>?'])

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tahun Akademik & Semester</h1>
            <p class="text-xs text-slate-500 mt-1">Pengaturan periode ajaran aktif, semester (Ganjil/Genap), dan tanggal efektif pembelajaran.</p>
        </div>
        @permission('create-academic-years')
        <div>
            <button type="button" @click="openModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Tahun Akademik</span>
            </button>
        </div>
        @endpermission
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Periode Terdaftar</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($academicYears->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tahun Akademik Aktif</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ $activeYear?->name ?? 'Belum Diatur' }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Semester Aktif Saat Ini</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1 tracking-tight">
                    {{ $activeYear ? 'Semester ' . ucfirst($activeYear->semester) : '-' }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Tahun Akademik</h3>
            <span class="text-xs text-slate-500">Total {{ $academicYears->count() }} periode terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Nama Tahun Akademik</th>
                        <th class="px-5 py-3">Rentang Tahun</th>
                        <th class="px-5 py-3">Tanggal Efektif</th>
                        <th class="px-5 py-3">Semester</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($academicYears as $year)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $year->is_active ? 'bg-emerald-50/20' : '' }}">
                            <!-- Name -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-lg {{ $year->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} font-bold flex items-center justify-center text-xs shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-xs">{{ $year->name }}</p>
                                        @if($year->is_active)
                                            <span class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">Periode Aktif Utama</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Rentang Tahun -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded font-mono font-semibold text-xs border border-slate-200">
                                    {{ $year->start_year }} - {{ $year->end_year }}
                                </span>
                            </td>

                            <!-- Tanggal Efektif -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-1.5 text-slate-600 font-medium text-xs">
                                    <span>{{ $year->start_date ? \Carbon\Carbon::parse($year->start_date)->format('d/m/Y') : '-' }}</span>
                                    <span class="text-slate-400">s/d</span>
                                    <span>{{ $year->end_date ? \Carbon\Carbon::parse($year->end_date)->format('d/m/Y') : '-' }}</span>
                                </div>
                            </td>

                            <!-- Semester -->
                            <td class="px-5 py-3.5">
                                @if(in_array(strtolower($year->semester), ['ganjil', '1']))
                                    <span class="px-2.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 text-[11px] font-semibold">
                                        Semester Ganjil
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-semibold">
                                        Semester Genap
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($year->is_active)
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">
                                        Aktif
                                    </span>
                                @else
                                    <div class="flex items-center justify-center space-x-2">
                                        <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">
                                            Nonaktif
                                        </span>
                                        <form action="{{ route('admin.academic-years.set-active', $year->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-bold text-emerald-700 hover:text-emerald-800 hover:underline px-2 py-0.5 bg-emerald-50 rounded border border-emerald-200 transition">
                                                Set Aktif
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>

                             <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @permission('edit-academic-years')
                                    <button type="button" @click="openModal(true, {{ $year->id }})"
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Tahun Akademik">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endpermission

                                    @permission('delete-academic-years')
                                    <button type="button" @click="confirmDelete({{ $year->id }}, '{{ addslashes($year->name) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors" title="Hapus Tahun Akademik">
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
                                Belum ada data tahun akademik yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create / Edit Academic Year Form Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="showModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm" x-text="editMode ? 'Edit Tahun Akademik' : 'Tambah Tahun Akademik Baru'"></h3>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="editMode ? '{{ route('admin.academic-years.update', ':id') }}'.replace(':id', formData.id) : '{{ route('admin.academic-years.store') }}'" method="POST" class="p-6 space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Nama Tahun Akademik -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Tahun Akademik <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="formData.name" placeholder="Contoh: 2025/2026" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Tahun Mulai -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tahun Mulai <span class="text-rose-500">*</span></label>
                        <input type="number" name="start_year" x-model="formData.start_year" placeholder="2025" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Tahun Akhir -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tahun Akhir <span class="text-rose-500">*</span></label>
                        <input type="number" name="end_year" x-model="formData.end_year" placeholder="2026" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" x-model="formData.start_date" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Tanggal Akhir -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Akhir <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" x-model="formData.end_date" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Semester -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Semester <span class="text-rose-500">*</span></label>
                    <select name="semester" x-model="formData.semester" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="ganjil">Semester Ganjil</option>
                        <option value="genap">Semester Genap</option>
                    </select>
                </div>

                <!-- Status Checkbox -->
                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" id="is_active_ay_chk" x-model="formData.is_active" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active_ay_chk" class="text-xs font-semibold text-slate-700 cursor-pointer">Set Sebagai Tahun Akademik Aktif Utama</label>
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

</div>

<script>
function academicYearData() {
    return {
        showModal: false,
        showDeleteModal: false,
        editMode: false,
        deleteTarget: null,
        deleteFormAction: '',
        formData: {
            id: null,
            name: '',
            start_year: new Date().getFullYear(),
            end_year: new Date().getFullYear() + 1,
            start_date: '',
            end_date: '',
            semester: 'ganjil',
            is_active: false
        },
        openModal(edit = false, yearId = null) {
            this.editMode = edit;
            if (edit && yearId) {
                const yearData = window.academicYearStore[yearId];
                this.formData = {
                    id: yearData.id,
                    name: yearData.name,
                    start_year: yearData.start_year,
                    end_year: yearData.end_year,
                    start_date: yearData.start_date ? yearData.start_date.substring(0, 10) : '',
                    end_date: yearData.end_date ? yearData.end_date.substring(0, 10) : '',
                    semester: yearData.semester || 'ganjil',
                    is_active: !!yearData.is_active
                };
            } else {
                this.formData = {
                    id: null,
                    name: '',
                    start_year: new Date().getFullYear(),
                    end_year: new Date().getFullYear() + 1,
                    start_date: '',
                    end_date: '',
                    semester: 'ganjil',
                    is_active: false
                };
            }
            this.showModal = true;
        },
        confirmDelete(yearId, yearName) {
            this.deleteTarget = { id: yearId, name: yearName };
            this.deleteFormAction = '{{ url('admin/academic-years') }}/' + yearId;
            this.showDeleteModal = true;
        }
    }
}

function initAcademicYearData() {
    window.academicYearStore = @json($academicYears->mapWithKeys(fn($y) => [$y->id => $y]));
}
</script>
@endsection
