@extends('layouts.admin')

@section('title', 'Presensi Mandiri Guru & Pegawai')
@section('page_title', 'Presensi Mandiri')

@section('content')
<div class="space-y-6" x-data="{ openPresensiModal: false, openEmployeePermitModal: false }">

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- HEADER & PROFILE CARD -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center font-black text-xl shadow-md shrink-0"
                 style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#3C50E0') }} 0%, {{ Setting::get('secondary_color', '#2563eb') }} 100%);">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 uppercase">
                        {{ $user->roles->pluck('name')->implode(', ') ?: 'Pendidik' }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1">
                    @if($user->nip)
                        <span class="font-mono">NIP: {{ $user->nip }}</span>
                        <span>&bull;</span>
                    @endif
                    <span>{{ $user->email }}</span>
                    <span>&bull;</span>
                    <span>{{ $schoolName }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Action Buttons & Live Server Clock -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
            <!-- Button: Absen Mandiri GPS Popup -->
            <button type="button" @click.stop="openPresensiModal = true"
                    class="px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-extrabold text-xs flex items-center justify-center gap-2 transition-all shadow-md shadow-indigo-600/20 cursor-pointer">
                <span class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-sm">📍</span>
                <span>Absen Mandiri GPS</span>
            </button>

            <!-- Button: Ajukan Izin / Sakit Popup -->
            <button type="button" @click.stop="openEmployeePermitModal = true"
                    class="px-4 py-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-xs font-extrabold flex items-center justify-center gap-2 transition-all shadow-xs cursor-pointer">
                <span>📝</span>
                <span>Ajukan Izin / Sakit</span>
            </button>

            <!-- Live Server Clock (WITA) -->
            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-slate-700/60 text-right min-w-[150px]">
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Waktu Server ({{ $timezoneLabel }})</span>
                <span class="text-xl font-black font-mono text-slate-900 dark:text-white" id="live-server-clock">
                    {{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}
                </span>
            </div>
        </div>
    </div>

    @if($todayAttendance && in_array($todayAttendance->status, ['sick', 'permission']))
    <!-- Status Izin Resmi Hari Ini -->
    <div class="p-4 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                📋
            </span>
            <div>
                <h4 class="text-xs font-extrabold text-indigo-900 dark:text-indigo-200">Status Kehadiran Hari Ini: {{ $todayAttendance->status_label }}</h4>
                <p class="text-[11px] text-indigo-700 dark:text-indigo-300">{{ $todayAttendance->notes }}</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300/40 shrink-0">
            Resmi Terverifikasi
        </span>
    </div>
    @endif

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- 3 KARTU SESI PRESENSI HARI INI -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- 1. Pagi (Masuk) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->check_in) ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">1. Sesi Pagi (Masuk)</span>
                @if($todayAttendance && $todayAttendance->check_in)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">✓ Sudah Hadir</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Absen</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->check_in) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->check_in ? substr($todayAttendance->check_in, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Batas: {{ $attendanceSettings['morning_late'] ?? '07:30' }} {{ $timezoneLabel }} (Buka: {{ $attendanceSettings['morning_open'] ?? '06:00' }} - {{ $attendanceSettings['morning_close'] ?? '11:59' }})
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->check_in) ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    🌅
                </div>
            </div>
        </div>

        <!-- 2. Siang (Dzuhur) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->midday_at) ? 'bg-amber-50/40 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/40' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">2. Sesi Siang (Dzuhur)</span>
                @if($todayAttendance && $todayAttendance->midday_at)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">✓ Sudah Hadir</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Absen</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->midday_at) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->midday_at ? substr($todayAttendance->midday_at, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Jadwal: {{ $attendanceSettings['dzuhur_open'] ?? '12:00' }} - {{ $attendanceSettings['dzuhur_close'] ?? '13:30' }} {{ $timezoneLabel }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->midday_at) ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    ☀️
                </div>
            </div>
        </div>

        <!-- 3. Sore (Pulang) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->check_out) ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-800/40' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">3. Sesi Sore (Pulang)</span>
                @if($todayAttendance && $todayAttendance->check_out)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">✓ Sudah Pulang</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Pulang</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->check_out) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->check_out ? substr($todayAttendance->check_out, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        KBM Selesai: {{ $attendanceSettings['afternoon_open'] ?? '14:00' }} - {{ $attendanceSettings['afternoon_close'] ?? '18:00' }} {{ $timezoneLabel }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->check_out) ? 'bg-indigo-500/10 text-indigo-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    🌇
                </div>
            </div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- REKAP KEHADIRAN BULAN INI -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Rekap Kehadiran Bulan Ini</h4>
                <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Persentase:</span>
                <span class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ $attendanceRate }}%</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40">
                <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase block">Tepat Waktu</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1 block">{{ $presentCount }} Hari</span>
            </div>
            <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40">
                <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 uppercase block">Terlambat</span>
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1 block">{{ $lateCount }} Hari</span>
            </div>
            <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/40">
                <span class="text-[10px] font-bold text-blue-700 dark:text-blue-300 uppercase block">Sakit</span>
                <span class="text-2xl font-black text-blue-600 dark:text-blue-400 font-mono mt-1 block">{{ $sickCount }} Hari</span>
            </div>
            <div class="p-4 rounded-2xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/40">
                <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase block">Izin</span>
                <span class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono mt-1 block">{{ $permissionCount }} Hari</span>
            </div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- RIWAYAT KEHADIRAN (14 HARI TERAKHIR) -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="tailadmin-card bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Riwayat Kehadiran Terakhir (14 Hari Terakhir)</h3>
            <span class="text-xs text-slate-500 font-medium">Data Kehadiran Pribadi Anda</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-[#24303F] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Pagi (Masuk)</th>
                        <th class="px-6 py-3.5">Siang (Dzuhur)</th>
                        <th class="px-6 py-3.5">Sore (Pulang)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Moda / Lokasi</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold font-mono">
                                {{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $att->check_in ? substr($att->check_in, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                {{ $att->midday_at ? substr($att->midday_at, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $att->check_out ? substr($att->check_out, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($att->status === 'present')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Tepat Waktu</span>
                                @elseif($att->status === 'late')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">Terlambat</span>
                                @elseif($att->status === 'sick')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">Sakit</span>
                                @elseif($att->status === 'permission')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">Izin</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600">Alpa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="capitalize font-medium">{{ $att->work_location === 'outstation' ? 'Dinas Luar' : ($att->work_location === 'home' ? 'WFH' : 'WFO Sekolah') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold {{ $att->method === 'fingerprint' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-cyan-100 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300' }}">
                                    {{ $att->method === 'fingerprint' ? 'Sidik Jari USB' : 'GPS Mobile' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($att->verification_status)
                                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-[11px] inline-flex items-center gap-1">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-xs">
                                Belum ada riwayat presensi yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- UNIVERSAL POPUP MODALS -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    @include('components.dashboard.presensi-modal')
    @include('components.dashboard.employee-permit-modal')

</div>
@endsection

@section('scripts')
<script>
// Standalone, self-running realtime live ticking clock (WITA)
(function() {
    let serverTimeStr = "{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}";
    let parts = serverTimeStr.split(':').map(Number);
    let totalSeconds = (parts[0] * 3600) + (parts[1] * 60) + (parts[2] || 0);

    function pad(n) {
        return n < 10 ? '0' + n : n;
    }

    function tick() {
        totalSeconds = (totalSeconds + 1) % 86400;
        let h = Math.floor(totalSeconds / 3600);
        let m = Math.floor((totalSeconds % 3600) / 60);
        let s = totalSeconds % 60;
        let timeFormatted = pad(h) + ':' + pad(m) + ':' + pad(s);

        let clockEl = document.getElementById('live-server-clock');
        if (clockEl) {
            clockEl.textContent = timeFormatted;
        }
    }

    setInterval(tick, 1000);
})();
</script>
@endsection
