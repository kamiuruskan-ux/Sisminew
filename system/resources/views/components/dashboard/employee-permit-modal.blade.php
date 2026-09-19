<!-- ============================================================ -->
<!-- UNIVERSAL PENGAJUAN IZIN PEGAWAI MODAL (RESPONSIVE POPUP) -->
<!-- ============================================================ -->
<div x-show="openEmployeePermitModal" x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="permit-modal-title" role="dialog" aria-modal="true">

    <!-- Fixed Backdrop Overlay (Hanya klik di luar popup/area gelap yang menutup popup) -->
    <div x-show="openEmployeePermitModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         @click="openEmployeePermitModal = false"></div>

    <!-- Centering Wrapper -->
    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="openEmployeePermitModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-[#1A222C] text-left shadow-2xl transition-all w-full max-w-lg p-5 sm:p-7 border border-slate-200 dark:border-[#2E3A47] space-y-4 my-8" 
             @click.stop>
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#2E3A47]">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                    📝
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Pengajuan Izin / Sakit Pegawai</h3>
                    <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD]">Diverifikasi &amp; Disahkan oleh Kepala Sekolah</p>
                </div>
            </div>
            <button type="button" @click="openEmployeePermitModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#24303F] text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Info Notification Banner -->
        <div class="p-3 bg-indigo-50/60 dark:bg-indigo-950/30 rounded-2xl border border-indigo-100 dark:border-indigo-800/40 flex items-start gap-2.5 text-xs text-indigo-900 dark:text-indigo-200">
            <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="leading-relaxed text-[11px]">
                Izin yang diajukan akan ditinjau Kepala Sekolah. Setelah disetujui, kehadiran Anda otomatis tercatat resmi pada presensi sekolah.
            </p>
        </div>

        <!-- Form Pengajuan Izin -->
        <form action="{{ route('admin.employee-permits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
            @csrf

            @php
                $isPrincipal = auth()->user()->hasRole('kepala-sekolah') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin');
            @endphp

            @if($isPrincipal)
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Pegawai / Guru <span class="text-rose-500">*</span></label>
                    <select name="user_id" required class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        <option value="">-- Ajukan Untuk Sendiri / Pilih Pegawai --</option>
                        @php
                            $allTeachersList = \App\Models\User::whereHas('roles', function ($q) {
                                $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'bendahara', 'guru-bk']);
                            })->orderBy('name')->get(['id', 'name', 'nip']);
                        @endphp
                        @foreach($allTeachersList as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->id === auth()->id() ? 'selected' : '' }}>{{ $emp->name }} (NIP: {{ $emp->nip ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Izin <span class="text-rose-500">*</span></label>
                    <select name="permit_type" required class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none font-semibold">
                        <option value="sakit">Sakit</option>
                        <option value="izin" selected>Izin Keperluan Pribadi</option>
                        <option value="cuti">Cuti Tahunan / Bersalin</option>
                        <option value="tugas_luar">Tugas / Dinas Luar</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kontak Darurat</label>
                    <input type="text" name="emergency_contact" placeholder="08xxxxxxxxxx" value="{{ auth()->user()->phone ?? '' }}" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mulai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 outline-none font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sampai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 outline-none font-semibold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Ketidakhadiran <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="2" required placeholder="Tuliskan keterangan detail alasan berhalangan hadir..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-750 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lampiran Berkas (Surat Dokter / Tugas)</label>
                <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                <p class="text-[10px] text-slate-400 mt-0.5">Format: JPG, PNG, WEBP, PDF, DOC (Maks. 10 MB)</p>
            </div>

            @if($isPrincipal)
                <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-200/50 dark:border-indigo-800/40">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_approve" value="1" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-[11px] font-bold text-indigo-900 dark:text-indigo-200">Langsung Setujui &amp; Sinkronkan ke Presensi Sekarang</span>
                    </label>
                </div>
            @endif

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-[#2E3A47]">
                <a href="{{ route('admin.employee-permits.index') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                    <span>Semua Riwayat Izin</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button" @click="openEmployeePermitModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-md shadow-indigo-600/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Kirim Permohonan</span>
                    </button>
                </div>
            </div>
        </form>

        </div>
    </div>
</div>
