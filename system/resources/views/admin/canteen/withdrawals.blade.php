@extends('layouts.admin')

@section('title', 'Penarikan Saldo Kantin')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Pencairan Saldo Kantin</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Verifikasi dan setujui permintaan penarikan saldo (withdrawal) dari pemilik stand kantin.</p>
        </div>
    </div>


    <!-- Status Tabs -->
    <div class="flex gap-2 bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl w-fit text-xs font-bold">
        <a href="{{ route('admin.canteen.withdrawals', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Semua
        </a>
        <a href="{{ route('admin.canteen.withdrawals', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Menunggu
        </a>
        <a href="{{ route('admin.canteen.withdrawals', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Disetujui
        </a>
        <a href="{{ route('admin.canteen.withdrawals', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Ditolak
        </a>
    </div>

    <!-- Withdrawals Table -->
    <div x-data="{ rejectModalOpen: false, rejectUrl: '', stallName: '', amountFormatted: '' }" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase font-extrabold text-[10px]">
                        <th class="py-3.5 px-4">Nama Stand & Pemilik</th>
                        <th class="py-3.5 px-4">Waktu Pengajuan</th>
                        <th class="py-3.5 px-4">Nominal Pencairan</th>
                        <th class="py-3.5 px-4">Rekening Tujuan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($withdrawals as $wd)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-colors">
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $wd->stall?->name ?? 'Stand Terhapus' }}</span>
                                <span class="text-[10px] text-slate-400 block">Pemilik: {{ $wd->stall?->owner_name ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-4 text-slate-600 dark:text-slate-400">
                                {{ $wd->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-4 font-black text-slate-900 dark:text-white">
                                Rp {{ number_format($wd->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-850 dark:text-slate-250 block">{{ $wd->bank_name }} - {{ $wd->bank_account_number }}</span>
                                <span class="text-[10px] text-slate-450 dark:text-slate-550 block">A/N: {{ $wd->bank_account_name }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @if($wd->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Menunggu</span>
                                @elseif($wd->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Selesai / Cair</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">Ditolak</span>
                                @endif

                                @if($wd->status === 'rejected' && $wd->rejection_reason)
                                    <span class="text-[10px] text-rose-500 block mt-1 font-medium italic max-w-xs">Alasan: {{ $wd->rejection_reason }}</span>
                                @endif

                                @if($wd->status === 'approved' && $wd->approved_at)
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Oleh: {{ $wd->approver?->name ?? 'Admin' }} pada {{ $wd->approved_at->format('d/m H:i') }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($wd->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <form method="POST" action="{{ route('admin.canteen.withdrawals.approve', $wd->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui penarikan saldo ini? Pastikan Anda sudah mentransfer uang ke rekening tujuan.')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[10.5px] transition-colors">
                                                Setujui / Cair
                                            </button>
                                        </form>

                                        <button type="button" 
                                                @click="rejectUrl = '{{ route('admin.canteen.withdrawals.reject', $wd->id) }}'; stallName = '{{ $wd->stall?->name }}'; amountFormatted = 'Rp {{ number_format($wd->amount, 0, ',', '.') }}'; rejectModalOpen = true"
                                                class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-[10.5px] transition-colors">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 dark:text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-450 dark:text-slate-500">
                                Tidak ada permintaan pencairan saldo ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                {{ $withdrawals->links() }}
            </div>
        @endif

        <!-- Rejection Reason Modal -->
        <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" @click="rejectModalOpen = false"></div>
            
            <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left align-middle shadow-2xl transition-all w-full max-w-md mx-4 sm:mx-auto border border-slate-200 dark:border-slate-800 my-auto z-10 p-6 text-slate-800 dark:text-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Tolak Permintaan Penarikan</h3>
                    <button type="button" @click="rejectModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition-colors">&times;</button>
                </div>

                <div class="bg-rose-50 dark:bg-rose-950/20 p-4 rounded-2xl text-xs space-y-1 text-rose-700 dark:text-rose-350 border border-rose-100 dark:border-rose-900/40 mb-4">
                    <p>Stand: <strong x-text="stallName"></strong></p>
                    <p>Nominal: <strong x-text="amountFormatted"></strong></p>
                    <p class="text-[10px] text-rose-600 dark:text-rose-450 mt-1">Catatan: Saldo akan dikembalikan secara otomatis ke stand kantin yang bersangkutan.</p>
                </div>

                <form method="POST" :action="rejectUrl" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Alasan Penolakan</label>
                        <textarea name="rejection_reason" required rows="3" placeholder="Tulis alasan penolakan penarikan saldo..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 font-bold text-xs text-slate-900 dark:text-white"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5">
                        <button type="button" @click="rejectModalOpen = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition-all">Batal</button>
                        <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl text-xs transition-all">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
