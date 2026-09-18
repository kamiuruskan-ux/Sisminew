@extends('layouts.admin')

@section('title', 'Manajemen Pesanan Kantin')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Daftar Pesanan Kantin Siswa</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pantau dan kelola pesanan makanan/minuman yang dipesan online oleh siswa.</p>
        </div>


    </div>

    <!-- Status Metric Cards / Tabs -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <a href="{{ route('admin.canteen.orders') }}" 
           class="p-4 rounded-2xl border transition-all {{ !request('status') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800' }}">
            <span class="text-[10px] uppercase tracking-wider font-extrabold block">Semua Pesanan</span>
            <span class="text-xl font-black block mt-1">{{ $counts['all'] }}</span>
        </a>

        <a href="{{ route('admin.canteen.orders', ['status' => 'pending']) }}" 
           class="p-4 rounded-2xl border transition-all {{ request('status') === 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800' }}">
            <span class="text-[10px] uppercase tracking-wider font-extrabold block text-amber-500 {{ request('status') === 'pending' ? 'text-white' : '' }}">Menunggu</span>
            <span class="text-xl font-black block mt-1">{{ $counts['pending'] }}</span>
        </a>

        <a href="{{ route('admin.canteen.orders', ['status' => 'processing']) }}" 
           class="p-4 rounded-2xl border transition-all {{ request('status') === 'processing' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800' }}">
            <span class="text-[10px] uppercase tracking-wider font-extrabold block text-blue-500 {{ request('status') === 'processing' ? 'text-white' : '' }}">Diproses</span>
            <span class="text-xl font-black block mt-1">{{ $counts['processing'] }}</span>
        </a>

        <a href="{{ route('admin.canteen.orders', ['status' => 'ready']) }}" 
           class="p-4 rounded-2xl border transition-all {{ request('status') === 'ready' ? 'bg-purple-600 text-white border-purple-600 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800' }}">
            <span class="text-[10px] uppercase tracking-wider font-extrabold block text-purple-500 {{ request('status') === 'ready' ? 'text-white' : '' }}">Siap Diambil</span>
            <span class="text-xl font-black block mt-1">{{ $counts['ready'] }}</span>
        </a>

        <a href="{{ route('admin.canteen.orders', ['status' => 'completed']) }}" 
           class="p-4 rounded-2xl border transition-all {{ request('status') === 'completed' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800' }}">
            <span class="text-[10px] uppercase tracking-wider font-extrabold block text-emerald-500 {{ request('status') === 'completed' ? 'text-white' : '' }}">Selesai</span>
            <span class="text-xl font-black block mt-1">{{ $counts['completed'] }}</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row gap-3">
        <form method="GET" action="{{ route('admin.canteen.orders') }}" class="flex-1 flex gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       @input.debounce.400ms="$el.closest('form').submit()"
                       x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                       placeholder="Cari Order ID, Kode QR, atau Nama Siswa..." 
                       class="w-full px-4 pr-8 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850">
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase font-extrabold text-[10px]">
                        <th class="py-3.5 px-4">Order ID & Waktu</th>
                        <th class="py-3.5 px-4">Siswa / Pembuat</th>
                        <th class="py-3.5 px-4">Item Dipesan</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Pembayaran</th>
                        <th class="py-3.5 px-4">Status Pesanan</th>
                        <th class="py-3.5 px-4 text-center">Aksi Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-colors">
                            <td class="py-4 px-4">
                                <span class="font-mono font-bold text-slate-900 dark:text-white block">{{ $ord->order_number }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $ord->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $ord->student?->user?->name ?? 'Walk-in / Umumu' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $ord->student?->class?->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-4 max-w-xs">
                                <ul class="space-y-0.5 text-slate-700 dark:text-slate-300">
                                    @foreach($ord->items as $it)
                                        <li class="truncate">&bull; {{ $it->item_name }} (x{{ $it->quantity }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-4 font-extrabold text-slate-900 dark:text-white">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    {!! $ord->payment_status_badge !!}
                                    <span class="text-[10px] text-slate-400 block uppercase font-mono">{{ $ord->payment_method }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                {!! $ord->order_status_badge !!}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center space-x-1">
                                    @if($ord->order_status === 'pending')
                                        <form method="POST" action="{{ route('admin.canteen.orders.status', $ord->id) }}">
                                            @csrf
                                            <input type="hidden" name="order_status" value="processing">
                                            <button type="submit" class="px-2.5 py-1 bg-blue-600 text-white font-bold text-[10px] rounded-lg">Proses</button>
                                        </form>
                                    @elseif($ord->order_status === 'processing')
                                        <form method="POST" action="{{ route('admin.canteen.orders.status', $ord->id) }}">
                                            @csrf
                                            <input type="hidden" name="order_status" value="ready">
                                            <button type="submit" class="px-2.5 py-1 bg-purple-600 text-white font-bold text-[10px] rounded-lg">Siap Diambil</button>
                                        </form>
                                    @elseif($ord->order_status === 'ready')
                                        <form method="POST" action="{{ route('admin.canteen.orders.status', $ord->id) }}">
                                            @csrf
                                            <input type="hidden" name="order_status" value="completed">
                                            <input type="hidden" name="payment_status" value="paid">
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white font-bold text-[10px] rounded-lg">Selesai</button>
                                        </form>
                                    @endif

                                    @if($ord->payment_status === 'unpaid')
                                        <form method="POST" action="{{ route('admin.canteen.orders.status', $ord->id) }}">
                                            @csrf
                                            <input type="hidden" name="payment_status" value="paid">
                                            <button type="submit" class="px-2.5 py-1 bg-amber-500 text-white font-bold text-[10px] rounded-lg">Tandai Lunas</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada pesanan kantin yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
