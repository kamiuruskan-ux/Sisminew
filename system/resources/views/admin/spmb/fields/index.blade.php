@extends('layouts.admin')

@section('title', 'Formulir & Pertanyaan SPMB')
@section('page_title', 'Formulir Pendaftaran SPMB')

@section('content')
<div class="space-y-6" x-data="{
    activeSection: 'all',
    showModal: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    modalTitle: 'Tambah Pertanyaan Formulir',
    submitButtonText: 'Simpan Pertanyaan',
    formAction: '{{ route('admin.spmb.fields.store') }}',
    formMethod: 'POST',
    formData: {
        id: null,
        label: '',
        section: 'student',
        type: 'text',
        options_text: '',
        placeholder: '',
        help_text: '',
        is_required: false,
        is_active: true,
        order: 1
    },
    openCreateModal(preselectedSection = 'student') {
        this.formAction = '{{ route('admin.spmb.fields.store') }}';
        this.formMethod = 'POST';
        this.modalTitle = 'Tambah Pertanyaan Formulir Baru';
        this.submitButtonText = 'Simpan Pertanyaan';
        this.formData = {
            id: null,
            label: '',
            section: preselectedSection !== 'all' ? preselectedSection : 'student',
            type: 'text',
            options_text: '',
            placeholder: '',
            help_text: '',
            is_required: false,
            is_active: true,
            order: 1
        };
        this.showModal = true;
    },
    openEditModal(item) {
        this.formAction = '{{ url('admin/spmb-fields') }}/' + item.encoded_id;
        this.formMethod = 'PUT';
        this.modalTitle = 'Edit Pertanyaan Formulir';
        this.submitButtonText = 'Perbarui Pertanyaan';
        
        let optionsText = '';
        if (Array.isArray(item.options)) {
            optionsText = item.options.join('\n');
        } else if (typeof item.options === 'string') {
            try {
                const parsed = JSON.parse(item.options);
                optionsText = Array.isArray(parsed) ? parsed.join('\n') : item.options;
            } catch (e) {
                optionsText = item.options;
            }
        }

        this.formData = {
            id: item.encoded_id,
            label: item.label || '',
            section: item.section || 'student',
            type: item.type || 'text',
            options_text: optionsText,
            placeholder: item.placeholder || '',
            help_text: item.help_text || '',
            is_required: Boolean(item.is_required),
            is_active: Boolean(item.is_active),
            order: item.order || 1
        };
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
    },
    confirmDelete(encodedId, label) {
        this.deleteTarget = { id: encodedId, label: label };
        this.deleteFormAction = '{{ url('admin/spmb-fields') }}/' + encodedId;
        this.showDeleteModal = true;
    }
}">

    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Formulir & Pertanyaan SPMB</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Atur redaksi, tambah pertanyaan baru, pilih jenis jawaban, atau nonaktifkan pertanyaan yang tidak diperlukan.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.spmb.settings') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-extrabold text-xs transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pengaturan Umum SPMB</span>
            </a>
            <button type="button" @click="openCreateModal(activeSection)" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-500/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Pertanyaan</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Section Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <button type="button" @click="activeSection = 'all'"
                :class="activeSection === 'all' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800'"
                class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all shrink-0 cursor-pointer">
            Semua Bagian ({{ $fields->count() }})
        </button>

        @foreach($sections as $secKey => $secTitle)
            @php $count = $fields->where('section', $secKey)->count(); @endphp
            <button type="button" @click="activeSection = '{{ $secKey }}'"
                    :class="activeSection === '{{ $secKey }}' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800'"
                    class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all shrink-0 cursor-pointer">
                {{ $secTitle }} ({{ $count }})
            </button>
        @endforeach
    </div>

    <!-- Questions Grid List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($fields as $field)
            @php
                $fieldEncodedId = encode_id($field->id);
                $jsonData = [
                    'id' => $field->id,
                    'encoded_id' => $fieldEncodedId,
                    'label' => $field->label,
                    'field_key' => $field->field_key,
                    'section' => $field->section,
                    'type' => $field->type,
                    'options' => $field->options,
                    'placeholder' => $field->placeholder,
                    'help_text' => $field->help_text,
                    'is_required' => (bool) $field->is_required,
                    'is_active' => (bool) $field->is_active,
                    'order' => (int) $field->order,
                ];
            @endphp
            <div x-show="activeSection === 'all' || activeSection === '{{ $field->section }}'"
                 class="group bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition-all flex flex-col justify-between">
                
                <div>
                    <!-- Header Badges -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2.5 py-1 text-[11px] font-extrabold rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800">
                                {{ $field->section_label }}
                            </span>
                            <span class="px-2.5 py-1 text-[11px] font-extrabold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $field->type_label }}
                            </span>
                            @if($field->is_required)
                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                    Wajib Diisi *
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500">
                                    Opsional
                                </span>
                            @endif
                        </div>

                        <!-- Active Toggle -->
                        <form action="{{ route('admin.spmb.fields.toggle-active', $fieldEncodedId) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold transition-all cursor-pointer {{ $field->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border border-slate-200 dark:border-slate-700' }}" title="Klik untuk mengubah status aktif">
                                <span class="w-1.5 h-1.5 rounded-full {{ $field->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                <span>{{ $field->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Question Label -->
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-snug">
                        {{ $field->label }}
                    </h3>

                    @if($field->help_text)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">
                            💡 {{ $field->help_text }}
                        </p>
                    @endif

                    <!-- Placeholder / Sample Options -->
                    <div class="mt-4 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs">
                        @if(in_array($field->type, ['select', 'radio', 'checkbox']) && !empty($field->options))
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Pilihan Jawaban ({{ count($field->options) }} Opsi):</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($field->options as $opt)
                                    <span class="px-2 py-0.5 rounded-md bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[11px] font-medium">
                                        {{ $opt }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Contoh Isian / Placeholder:</p>
                            <p class="text-slate-600 dark:text-slate-300 font-mono text-[11px]">{{ $field->placeholder ?: '(Tidak ada placeholder khusus)' }}</p>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span class="font-mono text-[11px]">ID Kolom: {{ $field->field_key }}</span>
                    
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click='openEditModal(@json($jsonData))' class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-extrabold transition-colors cursor-pointer flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit</span>
                        </button>
                        <button type="button" @click="confirmDelete('{{ $fieldEncodedId }}', '{{ addslashes($field->label) }}')" class="p-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-colors cursor-pointer" title="Hapus Pertanyaan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200/80 dark:border-slate-800">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Belum Ada Pertanyaan Khusus</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Tambahkan pertanyaan kustom pertama Anda untuk melengkapi formulir pendaftaran SPMB.</p>
                <button type="button" @click="openCreateModal()" class="mt-5 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-500/20 transition-all cursor-pointer">
                    Tambah Pertanyaan Sekarang
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Form Tambah / Edit Pertanyaan -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200/80 dark:border-slate-800">
                <form :action="formAction" method="POST">
                    @csrf
                    <input type="hidden" name="_method" :value="formMethod">

                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="modalTitle"></h3>
                        <button type="button" @click="closeModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        <!-- Redaksi Pertanyaan / Label -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Redaksi / Judul Pertanyaan <span class="text-rose-500">*</span></label>
                            <input type="text" name="label" x-model="formData.label" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Contoh: Nama Lengkap Ayah Kandung">
                        </div>

                        <!-- Grid Bagian & Tipe Input -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bagian Formulir <span class="text-rose-500">*</span></label>
                                <select name="section" x-model="formData.section" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="student">Data Pribadi Calon Siswa</option>
                                    <option value="parent">Data Orang Tua / Wali</option>
                                    <option value="religious">Keagamaan & Al-Qur'an</option>
                                    <option value="health">Kesehatan & Karakteristik</option>
                                    <option value="other">Kuesioner / Informasi Tambahan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tipe Input / Jawaban <span class="text-rose-500">*</span></label>
                                <select name="type" x-model="formData.type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="text">Teks Singkat (Text)</option>
                                    <option value="textarea">Teks Panjang / Paragraf (Textarea)</option>
                                    <option value="select">Pilihan Dropdown (Select)</option>
                                    <option value="radio">Pilihan Tunggal (Radio Button)</option>
                                    <option value="checkbox">Pilihan Centang Banyak (Checkbox)</option>
                                    <option value="number">Nomor / Angka (Number)</option>
                                    <option value="date">Tanggal (Date)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Opsi Pilihan (Jika Tipe Select / Radio / Checkbox) -->
                        <div x-show="['select', 'radio', 'checkbox'].includes(formData.type)">
                            <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Daftar Pilihan Jawaban <span class="text-rose-500">*</span></label>
                            <textarea name="options_text" x-model="formData.options_text" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 leading-relaxed" placeholder="Tuliskan setiap pilihan dalam satu baris, contoh:&#10;Lancar Membaca Al-Qur'an&#10;Sedang Iqro / Tilawati&#10;Belum Mengenal Huruf"></textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Pisahkan tiap pilihan jawaban dengan baris baru (Enter) atau tanda koma (,).</p>
                        </div>

                        <!-- Placeholder & Keterangan Bantuan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Teks Contoh (Placeholder)</label>
                                <input type="text" name="placeholder" x-model="formData.placeholder" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Contoh: Nama lengkap beserta gelar">
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Urutan Tampil</label>
                                <input type="number" name="order" x-model="formData.order" min="1" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Petunjuk Pengisian / Catatan Bantuan (Opsional)</label>
                            <input type="text" name="help_text" x-model="formData.help_text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Contoh: Tuliskan nama lengkap ayah sesuai KTP / Akta">
                        </div>

                        <!-- Checkboxes: Wajib Diisi & Status Aktif -->
                        <div class="pt-2 space-y-2 border-t border-slate-100 dark:border-slate-800">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_required" value="1" :checked="formData.is_required" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">Wajib Diisi oleh Pendaftar (Required *)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" :checked="formData.is_active" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">Aktifkan Pertanyaan Ini di Formulir</span>
                            </label>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                        <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-extrabold text-xs hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-500/20 transition-all" x-text="submitButtonText">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="showDeleteModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200/80 dark:border-slate-800 p-6">
                <form :action="deleteFormAction" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>

                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white text-center">Hapus Pertanyaan?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 text-center mt-1">
                        Apakah Anda yakin ingin menghapus pertanyaan <span class="font-bold text-slate-800 dark:text-slate-200" x-text="deleteTarget?.label"></span>? Jawaban pendaftar yang sudah ada untuk pertanyaan ini mungkin tidak akan ditampilkan lagi di formulir.
                    </p>

                    <div class="mt-6 flex items-center justify-center gap-3">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-xs hover:bg-slate-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-600/20 transition-all">
                            Ya, Hapus Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
