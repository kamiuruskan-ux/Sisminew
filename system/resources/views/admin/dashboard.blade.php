@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="space-y-6" x-data="{ createPermitModal: false, openPresensiModal: false, openEmployeePermitModal: false }">

    <!-- TailAdmin Unified Global Adaptive Hero Banner -->
    @include('components.dashboard.hero-banner')

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- DASHBOARD VIEW AUTO INCLUSION BASED ON ASSIGNED ROLE -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    @if(($defaultTab ?? 'admin') === 'bendahara')
        @include('admin.dashboards.bendahara')
    @elseif(($defaultTab ?? 'admin') === 'guru')
        @include('admin.dashboards.guru')
    @elseif(($defaultTab ?? 'admin') === 'bk')
        @include('admin.dashboards.bk')
    @elseif(($defaultTab ?? 'admin') === 'operator')
        @include('admin.dashboards.operator')
    @elseif(($defaultTab ?? 'admin') === 'staff')
        @include('admin.dashboards.staff')
    @else
        @include('admin.dashboards.admin')
    @endif

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- QUICK INPUT STUDENT PERMIT MODAL -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div x-show="createPermitModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white dark:bg-[#1A222C] rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-[#2E3A47] space-y-5 my-8" @click.outside="createPermitModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#2E3A47]">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                        📝
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Input Surat Izin / Sakit Siswa</h3>
                        <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">Pencatatan langsung permohonan izin atau surat sakit siswa</p>
                    </div>
                </div>
                <button type="button" @click="createPermitModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#24303F] text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-sm transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <form action="{{ route('admin.student-permits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#1C2434] dark:text-white uppercase tracking-wider mb-2">Pilih Siswa Target *</label>
                    <x-student-select-search :students="$students" name="student_id" selected="" label="" placeholder="-- Cari Nama / NISN / Kelas Siswa --" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#1C2434] dark:text-white uppercase tracking-wider mb-2">Jenis Izin *</label>
                        <select name="permit_type" required class="w-full text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] text-[#1C2434] dark:text-white focus:ring-2 focus:ring-[#3C50E0]">
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin Kepentingan</option>
                            <option value="dispensasi">Dispensasi Kegiatan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#1C2434] dark:text-white uppercase tracking-wider mb-2">Tanggal Mulai *</label>
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] text-[#1C2434] dark:text-white focus:ring-2 focus:ring-[#3C50E0]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#1C2434] dark:text-white uppercase tracking-wider mb-2">Tanggal Selesai *</label>
                        <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] text-[#1C2434] dark:text-white focus:ring-2 focus:ring-[#3C50E0]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#1C2434] dark:text-white uppercase tracking-wider mb-2">Alasan / Keterangan *</label>
                    <textarea name="reason" rows="2" required placeholder="Tuliskan alasan izin / sakit..." class="w-full text-xs font-medium px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] text-[#1C2434] dark:text-white focus:ring-2 focus:ring-[#3C50E0]"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#1C2434] dark:text-white uppercase tracking-wider mb-2">Lampiran Bukti (Opsional)</label>
                    <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#3C50E0]/10 file:text-[#3C50E0] hover:file:bg-[#3C50E0]/20">
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100 dark:border-[#2E3A47]">
                    <button type="button" @click="createPermitModal = false" class="px-4 py-2.5 rounded-xl text-xs font-extrabold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-[#3C50E0] hover:bg-[#3C50E0]/90 shadow-md shadow-[#3C50E0]/20 transition">
                        Simpan &amp; Validasi Izin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Universal Presensi Mandiri GPS Modal -->
    @include('components.dashboard.presensi-modal')

    <!-- Universal Pengajuan Izin Pegawai Modal -->
    @include('components.dashboard.employee-permit-modal')
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const isDarkMode = document.documentElement.classList.contains('dark');
const textColor = isDarkMode ? '#8A99AD' : '#64748B';
const borderColor = isDarkMode ? '#2E3A47' : '#E2E8F0';

/* Bar Chart: Students per Class */
@php
    $classLabels = $chartData['studentsPerClass']->pluck('name')->toArray();
    $classData = $chartData['studentsPerClass']->pluck('students_count')->toArray();
@endphp
const classLabels = @json($classLabels);
const classData = @json($classData);
const classCanvas = document.getElementById('studentsClassChart');
if (classCanvas && classData && classData.length > 0) {
    new Chart(classCanvas, {
        type: 'bar',
        data: {
            labels: classLabels,
            datasets: [{
                label: 'Jumlah Siswa',
                data: classData,
                backgroundColor: '#3C50E0',
                borderRadius: 8,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: borderColor }, 
                    border: { dash: [4, 4] },
                    ticks: { font: { size: 11 } }
                },
                x: { 
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

/* Donut Chart: SPMB Status */
@php
    $statusLabels = $chartData['spmbStatus']->pluck('status')->map(function($s) {
        return match($s) {
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            'submitted' => 'Menunggu',
            default => ucfirst($s),
        };
    })->toArray();
    $statusData = $chartData['spmbStatus']->pluck('count')->toArray();
@endphp
const statusLabels = @json($statusLabels);
const statusData = @json($statusData);
const spmbCanvas = document.getElementById('spmbStatusChart');
if (spmbCanvas && statusData && statusData.length > 0) {
    new Chart(spmbCanvas, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: ['#3C50E0', '#10B981', '#F59E0B', '#EF4444', '#64748B'],
                borderWidth: 3,
                borderColor: isDarkMode ? '#24303F' : '#FFFFFF'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        boxWidth: 10, 
                        usePointStyle: true, 
                        font: { size: 11, weight: '600' },
                        padding: 16
                    } 
                }
            }
        }
    });
}

/* Area Line Chart: Activity Posts Trend */
@php
    $postLabels = $chartData['postsPerMonth']->pluck('month')->toArray();
    $postData = $chartData['postsPerMonth']->pluck('count')->toArray();
@endphp
const postLabels = @json($postLabels);
const postData = @json($postData);
const trendCanvas = document.getElementById('postsTrendChart');
if (trendCanvas && postData && postData.length > 0) {
    const ctxTrend = trendCanvas.getContext('2d');
    const gradientFill = ctxTrend.createLinearGradient(0, 0, 0, 260);
    gradientFill.addColorStop(0, 'rgba(60, 80, 224, 0.25)');
    gradientFill.addColorStop(1, 'rgba(60, 80, 224, 0.0)');

    new Chart(trendCanvas, {
        type: 'line',
        data: {
            labels: postLabels,
            datasets: [{
                label: 'Jumlah Artikel',
                data: postData,
                borderColor: '#3C50E0',
                borderWidth: 3,
                backgroundColor: gradientFill,
                fill: true,
                tension: 0.35,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#FFFFFF',
                pointBorderColor: '#3C50E0',
                pointBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: borderColor }, 
                    border: { dash: [4, 4] },
                    ticks: { font: { size: 11 } }
                },
                x: { 
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}
</script>
@endsection
