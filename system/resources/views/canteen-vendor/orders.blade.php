@extends('layouts.canteen-vendor')

@section('title', 'Daftar Pesanan Masuk')
@section('header_title', 'Pesanan Masuk')

@section('content')
<div x-data="{ receiptOrder: null, cancelOrderModal: null }" class="space-y-5 pb-24 w-full">

    <!-- Top Search & Filter Control Bar -->
    <form method="GET" action="{{ route('canteen.vendor.orders') }}" class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
        <input type="hidden" name="status" value="{{ $status }}">
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            <!-- Search Bar -->
            <div class="sm:col-span-2 relative">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari No. Pesanan / Nama Siswa..." 
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Date Filter Dropdown -->
            <div>
                <select name="date" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>Semua Tanggal</option>
                    <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="yesterday" {{ $dateFilter === 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                    <option value="this_month" {{ $dateFilter === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Status Filter Pills -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1 scrollbar-none">
        @php
            $pills = [
                'all' => 'Semua',
                'pending' => 'Menunggu',
                'processing' => 'Diproses',
                'ready' => 'Siap Diambil',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];
        @endphp

        @foreach($pills as $key => $label)
            <a href="{{ route('canteen.vendor.orders', array_merge(request()->query(), ['status' => $key])) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all border flex items-center space-x-1.5 {{ $status === $key ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>{{ $label }}</span>
                @if(isset($statusCounts[$key]))
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === $key ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                        {{ $statusCounts[$key] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Orders Cards Grid -->
    @if($orders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
            @foreach($orders as $order)
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3.5 flex flex-col justify-between">
                    
                    <!-- Header Card: Order Number & Badges -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <div class="flex items-center justify-between sm:justify-start space-x-2">
                            <span class="font-mono font-black text-[11px] sm:text-xs text-indigo-600 dark:text-indigo-400 truncate max-w-[170px] sm:max-w-none">{{ $order->order_number }}</span>
                            <span class="text-[10px] text-slate-400 font-medium shrink-0">{{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex items-center space-x-1 shrink-0">
                            {!! $order->order_status_badge !!}
                            {!! $order->payment_status_badge !!}
                        </div>
                    </div>

                    <!-- Customer & Items Detailed Breakdown -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-xs font-extrabold text-slate-900 dark:text-white truncate">
                                Pemesan: {{ $order->student?->user?->name ?? 'Pelanggan POS' }}
                            </h4>
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg shrink-0">
                                {{ $order->student?->class?->name ?? 'Umum' }}
                            </span>
                        </div>
                        
                        <!-- Order Items List -->
                        <div class="space-y-1.5 p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300">
                                    <span class="font-medium truncate pr-2"><span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $item->quantity }}x</span> {{ $item->item_name }}</span>
                                    <span class="font-semibold text-slate-500 shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if($order->notes)
                            <div class="text-[11px] p-2 bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-200 rounded-xl font-medium line-clamp-2">
                                <span class="font-bold">Catatan:</span> "{{ $order->notes }}"
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer: Total & Actions -->
                    <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 dark:border-slate-800 gap-2">
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase font-bold block leading-none mb-1">Total Nominal</span>
                            <span class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 block truncate">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-2 shrink-0">
                            <!-- Receipt Slip Button -->
                            <button type="button" 
                                    @click="receiptOrder = {{ json_encode([
                                        'order_number' => $order->order_number,
                                        'created_at' => $order->created_at->format('d/m/Y H:i'),
                                        'student_name' => $order->student?->user?->name ?? 'Pelanggan POS',
                                        'student_class' => $order->student?->class?->name ?? '-',
                                        'payment_method' => $order->payment_method === 'savings_balance' ? 'Saldo Tabungan' : strtoupper($order->payment_method ?? 'TUNAI'),
                                        'total_amount' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                                        'items' => $order->items->map(fn($i) => [
                                            'name' => $i->item_name,
                                            'qty' => $i->quantity,
                                            'subtotal' => 'Rp ' . number_format($i->subtotal, 0, ',', '.')
                                        ])
                                    ]) }}" 
                                    class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200" 
                                    title="Cetak Struk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </button>

                            <!-- Status Workflow Buttons -->
                            <form method="POST" action="{{ route('canteen.vendor.orders.update-status', $order->id) }}" class="flex items-center space-x-1">
                                @csrf
                                @if($order->order_status === 'pending')
                                    <input type="hidden" name="order_status" value="processing">
                                    <button type="submit" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all">Mulai Proses</button>
                                @elseif($order->order_status === 'processing')
                                    <input type="hidden" name="order_status" value="ready">
                                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">Siap Diambil</button>
                                @elseif($order->order_status === 'ready')
                                    <input type="hidden" name="order_status" value="completed">
                                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">Tandai Selesai</button>
                                @else
                                    <span class="text-xs text-slate-400 font-bold uppercase">Selesai</span>
                                @endif
                            </form>

                            <!-- Cancel Order Button (Triggers HTML Modal) -->
                            @if(in_array($order->order_status, ['pending', 'processing']))
                                <button type="button" 
                                        @click="cancelOrderModal = {
                                            id: {{ $order->id }},
                                            order_number: '{{ $order->order_number }}',
                                            student_name: '{{ addslashes($order->student?->user?->name ?? 'Pelanggan POS') }}'
                                        }" 
                                        class="px-2.5 py-1.5 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 font-bold text-xs rounded-xl hover:bg-rose-100 transition-all" 
                                        title="Batalkan Pesanan">
                                    &times;
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-800 space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2v2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Tidak Ada Pesanan Ditemukan</p>
            <p class="text-[11px] text-slate-400">Ganti filter status atau pencarian untuk melihat pesanan lainnya.</p>
        </div>
    @endif

    <!-- HTML Cancel Order Confirmation Modal -->
    <div x-show="cancelOrderModal !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cancelOrderModal = null"></div>

        <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-center shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10">
            
            <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>

            <div class="space-y-1">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Batalkan Pesanan?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Pesanan <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400" x-text="cancelOrderModal?.order_number"></span> (<span x-text="cancelOrderModal?.student_name"></span>) akan dibatalkan.
                </p>
            </div>

            <form :action="'{{ url('/canteen-vendor/orders') }}/' + cancelOrderModal?.id + '/update-status'" method="POST" class="pt-2 flex items-center space-x-2">
                @csrf
                <input type="hidden" name="order_status" value="cancelled">
                <button type="button" @click="cancelOrderModal = null" class="flex-1 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-600/20">
                    Ya, Batalkan
                </button>
            </form>
        </div>
    </div>

    <!-- Receipt Slip Modal for Printing -->
    <div x-show="receiptOrder !== null" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" @click="receiptOrder = null"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-slate-900 p-6 text-left align-middle shadow-2xl transition-all w-full max-w-sm font-mono space-y-4 my-auto">
                
                <div class="text-center border-b border-dashed border-slate-300 pb-3">
                    <h3 class="text-base font-black uppercase">{{ $stall->name }}</h3>
                    <p class="text-[10px] text-slate-500">Struk Pesanan Kantin Sekolah</p>
                    <div class="text-[11px] mt-2 flex justify-between text-slate-600">
                        <span>No: <strong x-text="receiptOrder?.order_number"></strong></span>
                        <span x-text="receiptOrder?.created_at"></span>
                    </div>
                </div>

                <template x-if="receiptOrder">
                    <div class="space-y-3 text-xs">
                        <div class="text-[11px] space-y-0.5 border-b border-dashed border-slate-300 pb-2">
                            <p>Pemesan: <strong x-text="receiptOrder.student_name"></strong></p>
                            <p>Kelas: <strong x-text="receiptOrder.student_class"></strong></p>
                            <p>Bayar: <strong x-text="receiptOrder.payment_method"></strong></p>
                        </div>

                        <div class="space-y-1.5 border-b border-dashed border-slate-300 pb-3">
                            <template x-for="item in receiptOrder.items" :key="item.name">
                                <div class="flex justify-between text-xs">
                                    <span x-text="item.qty + 'x ' + item.name"></span>
                                    <span x-text="item.subtotal"></span>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between text-sm font-black pt-1">
                            <span>TOTAL:</span>
                            <span x-text="receiptOrder.total_amount"></span>
                        </div>
                    </div>
                </template>

                <div class="text-center text-[10px] text-slate-400 pt-2 border-t border-dashed border-slate-300">
                    <p>Terima Kasih - Selamat Menikmati!</p>
                </div>

                <div class="pt-2 flex justify-between space-x-2 no-print">
                    <button type="button" @click="window.print()" class="flex-1 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-xs">Cetak Struk</button>
                    <button type="button" @click="receiptOrder = null" class="py-2 px-4 bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
