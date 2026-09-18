@extends('layouts.admin')

@section('title', 'Asesmen & Bimbingan Karir BK')
@section('page_title', 'Asesmen Minat, Bakat & Karir Siswa')

@section('content')
<div class="space-y-6" x-data="{
    showModal: false,
    studentId: '',
    searchOpen: false,
    studentSearch: '',
    studentsList: {{ json_encode($students->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'nisn' => $s->nisn,
        'class_name' => $s->class->name ?? '-'
    ])) }},
    get selectedStudentName() {
        const found = this.studentsList.find(s => s.id == this.studentId);
        return found ? found.name + ' (NISN: ' + (found.nisn || '-') + ' • Kelas ' + found.class_name + ')' : '-- Pilih Siswa (Ketik Nama / NISN / Kelas) --';
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

    <!-- Top Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Asesmen Minat, Bakat & Bimbingan Karir Siswa</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Pemetaan potensi diri, tes minat bakat, sosiometri, rekomendasi jurusan perguruan tinggi & perencanaan karir masa depan.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" @click="showModal = true"
                    class="inline-flex items-center space-x-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-purple-600/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Input Asesmen / Tes Karir</span>
            </button>
            <a href="{{ route('admin.bk.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#1A222C] hover:bg-slate-200 text-[#1C2434] dark:text-white font-bold text-xs rounded-xl border border-[#E2E8F0] dark:border-[#2E3A47]">
                Kembali ke Layanan BK
            </a>
        </div>
    </div>


    <!-- Data Table -->
    <div class="tailadmin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-6 py-4">Siswa & Kelas</th>
                        <th class="px-6 py-4">Jenis Asesmen</th>
                        <th class="px-6 py-4">Judul / Instrumen BK</th>
                        <th class="px-6 py-4">Cita-cita & Karir Impian</th>
                        <th class="px-6 py-4">Rekomendasi Jurusan / Kuliah</th>
                        <th class="px-6 py-4">Guru BK / Konselor</th>
                        <th class="px-6 py-4 text-right">Tanggal Input</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($assessments as $item)
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 font-black flex items-center justify-center text-xs border border-purple-500/20 shrink-0">
                                        {{ strtoupper(substr($item->student->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#1C2434] dark:text-white">{{ $item->student->name ?? '-' }}</p>
                                        <p class="text-[10px] text-indigo-600 font-bold">Kelas {{ $item->student->class->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-200">
                                    {{ $item->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-[#1C2434] dark:text-white">
                                {{ $item->title }}
                            </td>
                            <td class="px-6 py-4 font-bold text-purple-600 dark:text-purple-400">
                                {{ $item->dream_career ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $item->recommended_major ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item->counselor->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-slate-400">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                Belum ada data asesmen minat bakat & karir siswa terinput.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assessments->hasPages())
            <div class="p-4 border-t border-[#E2E8F0] dark:border-[#2E3A47]">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Input Asesmen -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showModal = false"></div>

            <div x-show="showModal" class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-3xl transition-all">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Input Asesmen Minat, Bakat & Karir</h3>
                    <button type="button" @click="showModal = false" class="p-1 text-slate-400 hover:text-white">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.bk.assessments.store') }}" class="space-y-4">
                    @csrf
                    <x-student-select-search :students="$students" :selected="old('student_id', $selectedStudentId ?? '')" required label="Pilih Siswa" accent-color="purple" />

                    <!-- Jenis Asesmen -->
                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Jenis Instrumen Asesmen <span class="text-rose-500">*</span></label>
                        <select name="type" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs font-bold rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                            <option value="angket_minat">Angket Minat & Potensi Diri</option>
                            <option value="bakat_karier">Pemetaan Bakat & Perencanaan Karir</option>
                            <option value="sosiometri">Asesmen Sosiometri & Hubungan Sosial</option>
                            <option value="psikotes">Hasil Tes Psikologi / IQ / Minat Bakat</option>
                            <option value="observasi_perilaku">Observasi Perkembangan Perilaku</option>
                        </select>
                    </div>

                    <!-- Judul -->
                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Judul Asesmen / Sesi Bimbingan <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Pemetaan Pilihan PTN/PTS Kelas XII" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                    </div>

                    <!-- Cita-cita & Rekomendasi Jurusan -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Cita-Cita / Karir Impian</label>
                            <input type="text" name="dream_career" placeholder="Contoh: Software Engineer, Dokter, Pengusaha" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Rekomendasi Jurusan / Perguruan Tinggi</label>
                            <input type="text" name="recommended_major" placeholder="Contoh: Teknik Informatika ITB / Kedokteran UI" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                        </div>
                    </div>

                    <!-- Catatan Kelebihan & Area Pengembangan -->
                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Catatan Potensi & Kekuatan Siswa</label>
                        <textarea name="strength_notes" rows="2" placeholder="Catatan kelebihan, minat dominan, kecerdasan majemuk..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" :disabled="!studentId" class="px-5 py-2 bg-purple-600 text-white text-xs font-bold rounded-xl shadow-md">Simpan Asesmen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
