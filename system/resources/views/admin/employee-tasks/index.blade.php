@extends('layouts.admin')

@section('title', 'Checklist & Tugas Harian Pegawai')
@section('page_title', 'Tugas & Checklist Pegawai')

@section('content')
<div class="space-y-6 pb-20" x-data="{
    activeTab: '{{ $tab }}',
    openCreateModal: false,
    selectAllEmployees: false,
    selectedEmployees: [],
    completedCount: {{ $completedTodayCount }},
    totalCount: {{ $totalTodayCount }},
    percentage: {{ $progressPercentage }},
    isToggling: false,

    toggleTask(taskId, event) {
        if (this.isToggling) return;
        this.isToggling = true;

        fetch('{{ url('admin/employee-tasks') }}/' + taskId + '/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ date: '{{ $today }}' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.completedCount = data.completed_count;
                this.totalCount = data.total_count;
                this.percentage = data.percentage;
            } else {
                alert(data.message || 'Gagal mengubah status tugas.');
                event.target.checked = !event.target.checked;
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan.');
            event.target.checked = !event.target.checked;
        })
        .finally(() => {
            this.isToggling = false;
        });
    },

    toggleSelectAll() {
        if (this.selectAllEmployees) {
            this.selectedEmployees = [
                @foreach($employees as $emp)
                    '{{ $emp->id }}',
                @endforeach
            ];
        } else {
            this.selectedEmployees = [];
        }
    }
}">

    <!-- Page Header Banner -->
    <div class="tailadmin-card p-6 border-l-4 border-primary bg-gradient-to-r from-primary/10 via-primary/5 to-transparent">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-primary text-white">SOP &amp; KEDISIPLINAN</span>
                    <span class="text-xs text-slate-500 font-bold dark:text-slate-400">📅 {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Checklist &amp; Tugas Harian Pegawai</h1>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">
                    Pantau target kerja, checklist SOP harian, dan pencapaian tugas dewan guru serta staf.
                </p>
            </div>
            
            @if($isPrivileged)
            <div class="flex items-center gap-2">
                <button @click="openCreateModal = true" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-secondary text-white text-xs font-extrabold shadow-md shadow-primary/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Buat Tugas Pegawai (Bulk)</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3 text-emerald-800 dark:text-emerald-300 text-xs font-bold">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Dynamic Progress Bar Card (Hari Ini) -->
    <div class="tailadmin-card p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-primary">TARGET HARI INI</span>
                <h3 class="text-sm sm:text-base font-extrabold text-[#1C2434] dark:text-white">Progres Penyelesaian Tugas Hari Ini</h3>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-black border border-emerald-500/20" x-text="completedCount + ' Selesai dari ' + totalCount + ' Tugas'">
                    {{ $completedTodayCount }} Selesai dari {{ $totalTodayCount }} Tugas
                </span>
                <span class="text-sm font-black text-primary font-mono" x-text="percentage + '%'">{{ $progressPercentage }}%</span>
            </div>
        </div>

        <!-- Animated Progress Bar -->
        <div class="w-full h-3 bg-slate-100 dark:bg-strokedark rounded-full overflow-hidden p-0.5 border border-slate-200/60 dark:border-slate-700">
            <div class="h-full bg-gradient-to-r from-primary via-indigo-500 to-emerald-500 rounded-full transition-all duration-500" :style="`width: ${percentage}%`"></div>
        </div>
    </div>

    <!-- Filter Tabs (Berlangsung / Mendatang / Riwayat) -->
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-strokedark pb-2 overflow-x-auto gap-2">
        <div class="flex items-center gap-2 shrink-0">
            <button @click="activeTab = 'berlangsung'" :class="activeTab === 'berlangsung' ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-slate-100 dark:bg-boxdark text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5">
                <span>🔥</span>
                <span>Berlangsung (Hari Ini)</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-white/20 ml-1">{{ $todayTasks->count() }}</span>
            </button>

            <button @click="activeTab = 'mendatang'" :class="activeTab === 'mendatang' ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-slate-100 dark:bg-boxdark text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5">
                <span>🗓️</span>
                <span>Akan Datang</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-white/20 ml-1">{{ $upcomingTasks->count() }}</span>
            </button>

            <button @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-slate-100 dark:bg-boxdark text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5">
                <span>📜</span>
                <span>Riwayat / History</span>
            </button>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 1: BERLANGSUNG (HARI INI) -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'berlangsung'" class="space-y-3">
        @if($todayTasks->isEmpty())
            <div class="tailadmin-card p-10 text-center space-y-3">
                <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mx-auto shadow-sm">
                    🎉
                </div>
                <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Tidak Ada Tugas Hari Ini!</h3>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD] max-w-md mx-auto">
                    Semua checklist atau tugas telah tuntas, atau belum ada tugas harian yang dijadwalkan untuk hari ini.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($todayTasks as $task)
                    @php
                        $isDone = $task->isCompletedByUserOnDate(auth()->id(), $today);
                    @endphp
                    <div class="tailadmin-card p-4 sm:p-5 transition-all hover:shadow-md border-l-4 {{ $isDone ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/10' : ($task->priority === 'urgent' ? 'border-rose-500' : ($task->priority === 'high' ? 'border-amber-500' : 'border-primary')) }} space-y-3" x-data="{ checked: {{ $isDone ? 'true' : 'false' }} }">
                        
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <!-- Interactive Checkbox (Only Active for Today) -->
                                <label class="relative flex items-center cursor-pointer pt-0.5">
                                    <input type="checkbox" 
                                           :checked="checked" 
                                           @change="checked = !checked; toggleTask({{ $task->id }}, $event)"
                                           class="w-5 h-5 rounded-lg border-2 border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-strokedark dark:bg-boxdark cursor-pointer transition">
                                </label>

                                <div class="space-y-1">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-[#1C2434] dark:text-white transition-all" :class="{ 'line-through text-slate-400 dark:text-slate-500': checked }">
                                        {{ $task->title }}
                                    </h4>
                                    @if($task->description)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed" :class="{ 'text-slate-400': checked }">
                                            {{ $task->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $task->priority === 'urgent' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300' : ($task->priority === 'high' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300') }}">
                                    {{ $task->priority }}
                                </span>
                                @if($task->due_time)
                                    <span class="text-[10px] text-slate-400 font-mono font-bold flex items-center gap-1">
                                        <span>⏰</span> {{ substr($task->due_time, 0, 5) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Footer Details -->
                        <div class="border-t border-slate-100 dark:border-strokedark pt-2.5 flex items-center justify-between text-[11px] text-slate-400">
                            <span class="flex items-center gap-1">
                                <span>🏷️</span> {{ $task->category }}
                            </span>
                            <span class="flex items-center gap-1 font-bold" :class="checked ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'">
                                <span x-text="checked ? '✅ Selesai Dikerjakan' : '⏳ Belum Selesai'"></span>
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ============================================================ -->
    <!-- TAB 2: AKAN DATANG (MENDATANG) -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'mendatang'" class="space-y-3" style="display: none;">
        @if($upcomingTasks->isEmpty())
            <div class="tailadmin-card p-10 text-center space-y-2">
                <span class="text-3xl">📅</span>
                <h3 class="text-sm font-extrabold text-[#1C2434] dark:text-white">Tidak Ada Tugas Mendatang</h3>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">Belum ada jadwal tugas berulang atau tugas mendatang.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($upcomingTasks as $task)
                    <div class="tailadmin-card p-4 sm:p-5 opacity-90 hover:opacity-100 border border-slate-200 dark:border-strokedark space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <!-- Locked Checkbox for Future Dates -->
                                <div class="w-5 h-5 rounded-lg border-2 border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-xs shrink-0" title="Tugas ini terkunci karena hanya dapat dikerjakan pada tanggal jatuh tempo">
                                    🔒
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-[#1C2434] dark:text-white">{{ $task->title }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $task->description ?? 'Tidak ada catatan instruksi tambahan.' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 shrink-0">
                                {{ $task->recurrence === 'daily' ? 'Harian' : ($task->recurrence === 'weekly' ? 'Pekanan' : ($task->recurrence === 'monthly' ? 'Bulanan' : 'Sekali')) }}
                            </span>
                        </div>

                        <div class="border-t border-slate-100 dark:border-strokedark pt-2.5 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Mulai: {{ \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') }}</span>
                            <span class="text-amber-600 dark:text-amber-400 font-bold">🔒 Terkunci (Mendatang)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ============================================================ -->
    <!-- TAB 3: RIWAYAT / HISTORY -->
    <!-- ============================================================ -->
    <div x-show="activeTab === 'riwayat'" class="space-y-3" style="display: none;">
        <div class="tailadmin-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-strokedark flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-[#1C2434] dark:text-white">Log Riwayat Penyelesaian Tugas</h3>
                <span class="text-[11px] text-slate-400">Tercatat di Database</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-boxdark-2 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-100 dark:border-strokedark">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Nama Tugas</th>
                            <th class="py-3 px-4">Pegawai</th>
                            <th class="py-3 px-4">Waktu Selesai</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-strokedark">
                        @forelse($recentCompletions as $c)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-boxdark-2/50 transition">
                                <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($c->completion_date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-800 dark:text-white">
                                    {{ $c->task->title ?? 'Tugas Dihapus' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-semibold">
                                    {{ $c->user->name ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                    {{ $c->completed_at ? $c->completed_at->format('H:i') . ' WITA' : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        SELESAI
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada riwayat penyelesaian tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($recentCompletions->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-strokedark">
                    {{ $recentCompletions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL: BUAT TUGAS PEGAWAI (BULK & RECURRENCE) -->
    <!-- ============================================================ -->
    @if($isPrivileged)
    <div x-show="openCreateModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
        <div class="tailadmin-card w-full max-w-2xl p-6 space-y-5 bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark shadow-2xl rounded-3xl" @click.away="openCreateModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-strokedark pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                        📋
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-[#1C2434] dark:text-white">Buat Tugas / Checklist Pegawai</h3>
                        <p class="text-[11px] text-slate-400">Tugaskan ke satu atau banyak pegawai sekaligus</p>
                    </div>
                </div>
                <button @click="openCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.employee-tasks.store') }}" class="space-y-4 text-xs">
                @csrf

                <!-- Judul Tugas -->
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Judul Tugas / SOP <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Mengisi Jurnal Kelas & Absensi Siswa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                </div>

                <!-- Deskripsi -->
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Deskripsi / Instruksi</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan detail yang harus dikerjakan pegawai..." class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Kategori -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Kategori</label>
                        <select name="category" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white outline-none">
                            <option value="KBM">KBM &amp; Kelas</option>
                            <option value="SOP Santri">SOP Santri &amp; Adab</option>
                            <option value="Administrasi">Administrasi</option>
                            <option value="Kebersihan">Kebersihan &amp; Sarpras</option>
                            <option value="Umum" selected>Umum</option>
                        </select>
                    </div>

                    <!-- Prioritas -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Prioritas</label>
                        <select name="priority" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white outline-none">
                            <option value="low">Biasa (Low)</option>
                            <option value="medium" selected>Sedang (Medium)</option>
                            <option value="high">Tinggi (High)</option>
                            <option value="urgent">Mendesak (Urgent)</option>
                        </select>
                    </div>

                    <!-- Recurrence (Perulangan) -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Perulangan</label>
                        <select name="recurrence" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white outline-none">
                            <option value="daily" selected>Harian (Setiap Hari)</option>
                            <option value="weekly">Pekanan (Setiap Minggu)</option>
                            <option value="monthly">Bulanan (Setiap Bulan)</option>
                            <option value="none">Sekali Saja (Non-Berulang)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" required value="{{ $today }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white outline-none font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Tanggal Selesai (Opsional)</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white outline-none font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Batas Jam Tenggat</label>
                        <input type="time" name="due_time" value="16:00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white outline-none font-mono">
                    </div>
                </div>

                <!-- Bulk Employee Selector -->
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-strokedark">
                    <div class="flex items-center justify-between">
                        <label class="font-extrabold text-slate-800 dark:text-white">Pilih Pegawai Yang Ditugaskan <span class="text-rose-500">*</span></label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-primary font-bold">
                            <input type="checkbox" x-model="selectAllEmployees" @change="toggleSelectAll()" class="rounded text-primary focus:ring-primary">
                            <span>Pilih Semua Pegawai</span>
                        </label>
                    </div>

                    <div class="max-h-48 overflow-y-auto p-2 bg-slate-50 dark:bg-boxdark-2 rounded-2xl border border-slate-200 dark:border-strokedark grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($employees as $emp)
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-boxdark hover:bg-slate-100/80 dark:hover:bg-slate-700 cursor-pointer border border-slate-100 dark:border-strokedark text-xs transition">
                                <input type="checkbox" name="assignees[]" value="{{ $emp->id }}" x-model="selectedEmployees" class="rounded text-primary focus:ring-primary">
                                <div class="truncate">
                                    <p class="font-bold text-slate-800 dark:text-white truncate">{{ $emp->name }}</p>
                                    <p class="text-[10px] text-slate-400 capitalize">{{ $emp->roles->pluck('name')->first() ?? 'Pegawai' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-strokedark">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-secondary text-white font-extrabold shadow-md shadow-primary/30 transition flex items-center gap-1.5">
                        <span>💾</span> Simpan &amp; Distribusikan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
