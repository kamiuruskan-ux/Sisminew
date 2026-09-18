@extends('layouts.admin')

@section('title', 'Gelombang Pendaftaran SPMB')
@section('page_title', 'Gelombang SPMB')

@section('content')
<div class="space-y-6" x-data="waveData()" x-init="initWaveData()">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Gelombang Pendaftaran SPMB</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola periode jalur penerimaan siswa baru, kuota daya tampung, dan status gelombang.</p>
        </div>
        <div>
            <button type="button" @click="openModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Gelombang Baru</span>
            </button>
        </div>
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Gelombang</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($waves->total()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gelombang Aktif</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($waves->where('status', 'active')->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gelombang Closed / Ditutup</p>
                <p class="text-2xl font-bold text-slate-600 mt-1 tracking-tight">{{ number_format($waves->where('status', 'closed')->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Gelombang Pendaftaran</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $waves->firstItem() ?? 0 }} - {{ $waves->lastItem() ?? 0 }} dari {{ $waves->total() }} gelombang</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Gelombang SPMB</th>
                        <th class="px-5 py-3">Tahun Akademik</th>
                        <th class="px-5 py-3">Periode Tanggal Efektif</th>
                        <th class="px-5 py-3 text-center">Kuota Daya Tampung</th>
                        <th class="px-5 py-3 text-right">Potongan SPP</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($waves as $wave)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Name -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs border border-indigo-100 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-xs">{{ $wave->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">Tahun: {{ $wave->year }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Academic Year -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded font-semibold text-xs border border-slate-200">
                                    {{ $wave->academicYear->name ?? 'Tahun ' . $wave->year }}
                                </span>
                            </td>

                            <!-- Periode -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-1.5 text-slate-600 font-medium text-xs">
                                    <span>{{ $wave->start_date ? $wave->start_date->format('d/m/Y') : '-' }}</span>
                                    <span class="text-slate-400">s/d</span>
                                    <span>{{ $wave->end_date ? $wave->end_date->format('d/m/Y') : '-' }}</span>
                                </div>
                            </td>

                            <!-- Kuota -->
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded font-bold text-xs border border-indigo-100">
                                    {{ $wave->quota ? number_format($wave->quota) . ' Siswa' : 'Tanpa Batas' }}
                                </span>
                            </td>

                            <!-- Potongan SPP -->
                            <td class="px-5 py-3.5 text-right font-bold text-emerald-700">
                                Rp {{ number_format($wave->spp_discount, 0, ',', '.') }}
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($wave->status === 'active')
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">
                                        Active
                                    </span>
                                @elseif($wave->status === 'closed')
                                    <span class="px-2.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold uppercase">
                                        Closed
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <button type="button" @click="openModal(true, {{ $wave->id }})"
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Gelombang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button type="button" @click="confirmDelete({{ $wave->id }}, '{{ addslashes($wave->name) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors" title="Hapus Gelombang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Belum ada gelombang pendaftaran yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($waves->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $waves->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Wave Form Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="showModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm" x-text="editMode ? 'Edit Gelombang SPMB' : 'Tambah Gelombang SPMB Baru'"></h3>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="editMode ? '{{ route('admin.waves.update', ':id') }}'.replace(':id', formData.id) : '{{ route('admin.waves.store') }}'" method="POST" class="p-6 space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Nama Gelombang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Gelombang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="formData.name" placeholder="Contoh: Gelombang 1 (Jalur Prestasi)" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <!-- Tahun Akademik -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tahun Akademik <span class="text-rose-500">*</span></label>
                    <select name="academic_year_id" x-model="formData.academic_year_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="">Pilih Tahun Akademik</option>
                        @foreach(\App\Models\AcademicYear::orderBy('name', 'desc')->get() as $ay)
                            <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                        @endforeach
                    </select>
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

                <div class="grid grid-cols-2 gap-3">
                    <!-- Potongan SPP -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Potongan SPP (Rp)</label>
                        <input type="number" name="spp_discount" x-model="formData.spp_discount" placeholder="Contoh: 100000" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-bold text-emerald-600">
                    </div>

                    <!-- Kuota -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kuota Siswa</label>
                        <input type="number" name="quota" x-model="formData.quota" placeholder="100" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Gelombang <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="formData.status" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            <option value="active">Active (Aktif)</option>
                            <option value="draft">Draft (Konsep)</option>
                            <option value="closed">Closed (Ditutup)</option>
                        </select>
                    </div>
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
            <h3 class="text-base font-bold text-slate-900">Hapus Gelombang SPMB?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus gelombang <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
function waveData() {
    return {
        showModal: false,
        showDeleteModal: false,
        editMode: false,
        deleteTarget: null,
        deleteFormAction: '',
        formData: { id: null, name: '', academic_year_id: '', start_date: '', end_date: '', quota: '', spp_discount: '', status: 'draft' },
        openModal(edit = false, waveId = null) {
            this.editMode = edit;
            if (edit && waveId) {
                const waveData = window.waveDataStore[waveId];
                this.formData = {
                    id: waveData.id,
                    name: waveData.name,
                    academic_year_id: waveData.academic_year_id ?? '',
                    start_date: waveData.start_date ? waveData.start_date.substring(0, 10) : '',
                    end_date: waveData.end_date ? waveData.end_date.substring(0, 10) : '',
                    quota: waveData.quota ?? '',
                    spp_discount: waveData.spp_discount ?? 0,
                    status: waveData.status || 'draft'
                };
            } else {
                this.formData = { id: null, name: '', academic_year_id: '', start_date: '', end_date: '', quota: '', spp_discount: '', status: 'draft' };
            }
            this.showModal = true;
        },
        confirmDelete(waveId, waveName) {
            this.deleteTarget = { id: waveId, name: waveName };
            this.deleteFormAction = '{{ url('admin/waves') }}/' + waveId;
            this.showDeleteModal = true;
        }
    }
}

function initWaveData() {
    window.waveDataStore = @json($waves->getCollection()->mapWithKeys(fn($w) => [$w->id => $w]));
}
</script>
@endsection
