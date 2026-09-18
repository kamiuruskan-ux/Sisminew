@extends('layouts.admin')

@section('title', 'Data Jurusan')
@section('page_title', 'Manajemen Jurusan')

@section('content')
<div class="space-y-6" x-data="majorData()" x-init="initMajorData()">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Jurusan & Program Keahlian</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data jurusan keahlian siswa, kode jurusan, dan alokasi kelas.</p>
        </div>
        @permission('create-majors')
        <div>
            <button type="button" @click="openModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Jurusan Baru</span>
            </button>
        </div>
        @endpermission
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Jurusan</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($totalMajors) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Program Kejuruan (SMK)</p>
                <p class="text-2xl font-bold text-amber-600 mt-1 tracking-tight">{{ number_format($vocationalCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Program Akademik (SMA/SMP)</p>
                <p class="text-2xl font-bold text-blue-600 mt-1 tracking-tight">{{ number_format($academicCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar (Real-Time Auto Submit) -->
    <div class="bg-white dark:bg-[#24303F] rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.majors.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Cari Jurusan</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama atau kode..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 pr-8 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    @if(request('search'))
                        <a href="{{ route('admin.majors.index', request()->except('search')) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Filter Type -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tipe Program</label>
                <select name="type" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Tipe Program</option>
                    <option value="academic" {{ request('type') === 'academic' ? 'selected' : '' }}>Akademik (SMA/SMP)</option>
                    <option value="vocational" {{ request('type') === 'vocational' ? 'selected' : '' }}>Kejuruan (SMK)</option>
                </select>
            </div>

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
            <h3 class="font-bold text-slate-900 text-sm">Daftar Jurusan & Program Keahlian</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $majors->firstItem() ?? 0 }} - {{ $majors->lastItem() ?? 0 }} dari {{ $majors->total() }} jurusan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Nama Jurusan & Kode</th>
                        <th class="px-5 py-3">Tipe Program</th>
                        <th class="px-5 py-3">Alokasi Kelas & Siswa</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($majors as $major)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Major Name & Code -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-xs border border-blue-100 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-xs">{{ $major->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">KODE: {{ $major->code ?? strtoupper($major->slug) }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="px-5 py-3.5">
                                @if($major->type === 'vocational')
                                    <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-semibold">
                                        Kejuruan (SMK)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-semibold">
                                        Akademik (SMA/SMP)
                                    </span>
                                @endif
                            </td>

                            <!-- Allocation (Classes & Students Count) -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-2 text-xs font-semibold">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded border border-slate-200">
                                        {{ number_format($major->classes_count ?? 0) }} Kelas
                                    </span>
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded border border-slate-200">
                                        {{ number_format($major->students_count ?? 0) }} Siswa
                                    </span>
                                </div>
                            </td>

                            <!-- Description -->
                            <td class="px-5 py-3.5">
                                <p class="text-xs text-slate-600 truncate max-w-xs">{{ $major->description ?? '-' }}</p>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($major->is_active)
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
                                    @permission('edit-majors')
                                    <button type="button" @click="openModal(true, {{ $major->id }})"
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Jurusan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endpermission

                                    @permission('delete-majors')
                                    <button type="button" @click="confirmDelete({{ $major->id }}, '{{ addslashes($major->name) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors" title="Hapus Jurusan">
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
                                Tidak ada data jurusan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($majors->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $majors->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Major Form Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="showModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm" x-text="editMode ? 'Edit Jurusan' : 'Tambah Jurusan Baru'"></h3>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="editMode ? '{{ route('admin.majors.update', ':id') }}'.replace(':id', formData.id) : '{{ route('admin.majors.store') }}'" method="POST" class="p-6 space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Nama Jurusan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Jurusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="formData.name" placeholder="Contoh: Teknik Komputer & Jaringan, IPA" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Kode Jurusan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Jurusan</label>
                        <input type="text" name="code" x-model="formData.code" placeholder="TKJ, IPA, IPS" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 uppercase">
                    </div>

                    <!-- Tipe Program -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Program <span class="text-rose-500">*</span></label>
                        <select name="type" x-model="formData.type" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            <option value="academic">Akademik (SMA/SMP)</option>
                            <option value="vocational">Kejuruan (SMK)</option>
                        </select>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Jurusan</label>
                    <textarea name="description" x-model="formData.description" rows="3" placeholder="Keterangan mengenai jurusan..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                <!-- Status Checkbox -->
                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" id="is_active_major_chk" x-model="formData.is_active" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active_major_chk" class="text-xs font-semibold text-slate-700 cursor-pointer">Jurusan Aktif</label>
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
            <h3 class="text-base font-bold text-slate-900">Hapus Data Jurusan?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus jurusan <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
function majorData() {
    return {
        showModal: false,
        showDeleteModal: false,
        editMode: false,
        deleteTarget: null,
        deleteFormAction: '',
        formData: { id: null, name: '', code: '', type: 'academic', description: '', is_active: true },
        openModal(edit = false, majorId = null) {
            this.editMode = edit;
            if (edit && majorId) {
                const majorData = window.majorDataStore[majorId];
                this.formData = {
                    id: majorData.id,
                    name: majorData.name,
                    code: majorData.code || '',
                    type: majorData.type || 'academic',
                    description: majorData.description || '',
                    is_active: !!majorData.is_active
                };
            } else {
                this.formData = { id: null, name: '', code: '', type: 'academic', description: '', is_active: true };
            }
            this.showModal = true;
        },
        confirmDelete(majorId, majorName) {
            this.deleteTarget = { id: majorId, name: majorName };
            this.deleteFormAction = '{{ url('admin/majors') }}/' + majorId;
            this.showDeleteModal = true;
        }
    }
}

function initMajorData() {
    window.majorDataStore = @json($majors->getCollection()->mapWithKeys(fn($m) => [$m->id => $m]));
}
</script>
@endsection
