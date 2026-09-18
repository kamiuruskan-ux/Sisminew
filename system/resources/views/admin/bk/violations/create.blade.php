@extends('layouts.admin')

@section('title', 'Catat Pelanggaran Siswa Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    studentId: '{{ old('student_id', $selectedStudentId ?? '') }}',
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
            <div class="w-12 h-12 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center font-bold text-xl border border-rose-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-[#1C2434] dark:text-white">Catat Pelanggaran Siswa Baru</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Input kejadian pelanggaran tata tertib, penetapan poin sanksi, dan status tindakan BK</p>
            </div>
        </div>

        <a href="{{ route('admin.bk.violations.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-[#2E3A47] transition">
            Kembali
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white dark:bg-[#1A222C] p-6 sm:p-8 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs">
        <form action="{{ route('admin.bk.violations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Siswa & Kategori -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-student-select-search :students="$students" :selected="old('student_id', $selectedStudentId ?? '')" required label="Pilih Siswa" accent-color="rose" />

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Kategori Pelanggaran (Opsional)
                    </label>
                    <select name="violation_category_id" id="category_select" class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white focus:ring-2 focus:ring-rose-500">
                        <option value="" data-points="0">-- Pilih Kategori Master (Auto-fill Poin) --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-points="{{ $cat->points }}" {{ old('violation_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} (+{{ $cat->points }} Poin &bull; {{ strtoupper($cat->level) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 2: Judul, Tanggal & Poin -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Tanggal Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="violation_date" value="{{ old('violation_date', date('Y-m-d')) }}" required 
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white font-mono">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Poin Pelanggaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="points" id="points_input" min="0" value="{{ old('points', 5) }}" required 
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-extrabold text-rose-600 dark:text-rose-400">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Status Tindakan BK <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-bold text-slate-800 dark:text-white">
                        <option value="processed" {{ old('status') == 'processed' ? 'selected' : '' }}>Diproses BK / Pembinaan</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending / Menunggu Klarifikasi</option>
                        <option value="sp1" {{ old('status') == 'sp1' ? 'selected' : '' }}>Surat Peringatan 1 (SP-1)</option>
                        <option value="sp2" {{ old('status') == 'sp2' ? 'selected' : '' }}>Surat Peringatan 2 (SP-2)</option>
                        <option value="sp3" {{ old('status') == 'sp3' ? 'selected' : '' }}>Surat Peringatan 3 (SP-3 / Skorsing)</option>
                        <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>Resolved (Selesai & Pembinaan Tuntas)</option>
                    </select>
                </div>
            </div>

            <!-- Section 3: Judul Pelanggaran -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Nama / Judul Pelanggaran <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Terlambat Masuk Sekolah 30 Menit / Tidak Mengikuti Upacara" 
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white focus:ring-2 focus:ring-rose-500">
            </div>

            <!-- Section 4: Catatan & Sanksi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Kronologi / Catatan Kejadian
                    </label>
                    <textarea name="notes" rows="4" placeholder="Jelaskan secara singkat kronologi atau latar belakang kejadian pelanggaran..." 
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white leading-relaxed">{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Sanksi / Tindakan Yang Diberikan
                    </label>
                    <textarea name="penalty" rows="4" placeholder="Contoh: Teguran lisan, pembersihan area perpustakaan, pemanggilan orang tua..." 
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white leading-relaxed">{{ old('penalty') }}</textarea>
                </div>
            </div>

            <!-- Section 5: Lampiran Dokumen / Foto Bukti -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Unggah Bukti Foto / Dokumen BAP (Opsional)
                </label>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.docx" 
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-500/10 file:text-rose-600 hover:file:bg-rose-500/20 cursor-pointer">
                <p class="text-[10px] text-slate-400 mt-1">Format diperbolehkan: JPG, PNG, PDF, DOCX (Maksimal 5MB)</p>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-200 dark:border-[#2E3A47] flex items-center justify-end space-x-3">
                <a href="{{ route('admin.bk.violations.index') }}" class="px-5 py-3 border border-slate-200 dark:border-[#2E3A47] text-slate-600 dark:text-slate-300 text-xs font-extrabold rounded-xl hover:bg-slate-100 dark:hover:bg-[#24303F] transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                    Simpan Catatan Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelectEl = document.getElementById('category_select');
        if (categorySelectEl) {
            categorySelectEl.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const points = selected ? selected.getAttribute('data-points') : 0;
                if (points && points > 0) {
                    document.getElementById('points_input').value = points;
                }
            });
        }
    });
</script>
@endsection
