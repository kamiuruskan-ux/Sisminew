@extends('layouts.admin')

@section('title', 'Edit Pelanggaran Siswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    studentId: '{{ old('student_id', $violation->student_id) }}',
    searchOpen: false,
    studentSearch: '',
    studentsList: {{ json_encode($students->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->user->name ?? $s->nisn ?? 'Siswa',
        'nisn' => $s->nisn ?? '-',
        'class_name' => $s->class->name ?? 'Tanpa Kelas'
    ])) }},
    get selectedStudentName() {
        const found = this.studentsList.find(s => s.id == this.studentId);
        return found ? found.name + ' (NISN: ' + (found.nisn || '-') + ' • Kelas ' + found.class_name + ')' : '-- Cari & Pilih Siswa (Nama / NISN / Kelas) --';
    },
    get filteredStudents() {
        if (!this.studentSearch) return this.studentsList;
        return this.studentsList.filter(s => 
            s.name.toLowerCase().includes(this.studentSearch.toLowerCase()) || 
            (s.nisn && s.nisn.toLowerCase().includes(this.studentSearch.toLowerCase())) ||
            (s.class_name && s.class_name.toLowerCase().includes(this.studentSearch.toLowerCase()))
        );
    }
}">
    <!-- Header Bar -->
    <div class="flex items-center justify-between bg-white dark:bg-[#1A222C] p-6 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center font-bold text-xl border border-amber-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-[#1C2434] dark:text-white">Edit Pelanggaran Siswa</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Perbarui catatan kejadian, sanksi, atau penyesuaian poin sanksi siswa</p>
            </div>
        </div>

        <a href="{{ route('admin.bk.violations.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-[#2E3A47] transition">
            Kembali
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white dark:bg-[#1A222C] p-6 sm:p-8 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs">
        <form action="{{ route('admin.bk.violations.update', $violation->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Siswa & Kategori -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-student-select-search :students="$students" :selected="old('student_id', $violation->student_id)" required label="Pilih Siswa" accent-color="amber" />

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Kategori Pelanggaran
                    </label>
                    <select name="violation_category_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white">
                        <option value="">-- Pilih Kategori Master --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('violation_category_id', $violation->violation_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} (+{{ $cat->points }} Poin)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 2: Tanggal, Poin, & Status -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Tanggal Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="violation_date" value="{{ old('violation_date', $violation->violation_date->format('Y-m-d')) }}" required 
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white font-mono">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Poin Pelanggaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="points" min="0" value="{{ old('points', $violation->points) }}" required 
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-extrabold text-amber-600 dark:text-amber-400">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Status Tindakan BK <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-bold text-slate-800 dark:text-white">
                        <option value="processed" {{ old('status', $violation->status) == 'processed' ? 'selected' : '' }}>Diproses BK / Pembinaan</option>
                        <option value="pending" {{ old('status', $violation->status) == 'pending' ? 'selected' : '' }}>Pending / Menunggu Klarifikasi</option>
                        <option value="sp1" {{ old('status', $violation->status) == 'sp1' ? 'selected' : '' }}>Surat Peringatan 1 (SP-1)</option>
                        <option value="sp2" {{ old('status', $violation->status) == 'sp2' ? 'selected' : '' }}>Surat Peringatan 2 (SP-2)</option>
                        <option value="sp3" {{ old('status', $violation->status) == 'sp3' ? 'selected' : '' }}>Surat Peringatan 3 (SP-3 / Skorsing)</option>
                        <option value="resolved" {{ old('status', $violation->status) == 'resolved' ? 'selected' : '' }}>Resolved (Selesai & Pembinaan Tuntas)</option>
                    </select>
                </div>
            </div>

            <!-- Section 3: Judul Pelanggaran -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Nama / Judul Pelanggaran <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $violation->title) }}" required placeholder="Contoh: Terlambat Masuk Sekolah 30 Menit / Tidak Mengikuti Upacara" 
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500">
            </div>

            <!-- Section 4: Catatan & Sanksi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Kronologi / Catatan Kejadian
                    </label>
                    <textarea name="notes" rows="4" placeholder="Jelaskan secara singkat kronologi atau latar belakang kejadian pelanggaran..." 
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white leading-relaxed">{{ old('notes', $violation->notes) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Sanksi / Tindakan Yang Diberikan
                    </label>
                    <textarea name="penalty" rows="4" placeholder="Contoh: Teguran lisan, pembersihan area perpustakaan, pemanggilan orang tua..." 
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white leading-relaxed">{{ old('penalty', $violation->penalty) }}</textarea>
                </div>
            </div>

            <!-- Section 5: Lampiran Dokumen / Foto Bukti -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Unggah Bukti Baru (Kosongkan jika tidak diubah)
                </label>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.docx" 
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500/10 file:text-amber-600 hover:file:bg-amber-500/20 cursor-pointer">
                @if($violation->attachment)
                    <p class="text-[11px] text-emerald-600 font-bold mt-1">Berkas lampiran saat ini tersedia.</p>
                @endif
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-200 dark:border-[#2E3A47] flex items-center justify-end space-x-3">
                <a href="{{ route('admin.bk.violations.index') }}" class="px-5 py-3 border border-slate-200 dark:border-[#2E3A47] text-slate-600 dark:text-slate-300 text-xs font-extrabold rounded-xl hover:bg-slate-100 dark:hover:bg-[#24303F] transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                    Perbarui Catatan Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
