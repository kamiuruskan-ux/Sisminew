<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CanteenCategory;
use App\Models\CanteenItem;
use App\Models\CanteenOrder;
use App\Models\CanteenOrderItem;
use App\Models\CanteenStall;
use App\Models\CanteenTransaction;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentCanteenController extends Controller
{
    /**
     * Display Student Canteen Marketplace.
     */
    public function index(Request $request)
    {
        $categories = CanteenCategory::where('is_active', true)->withCount(['activeItems'])->get();
        $stalls = \App\Models\CanteenStall::where('is_active', true)->withCount(['activeItems'])->get();

        $query = CanteenItem::where('is_available', true)->with(['category', 'stall']);

        $selectedStall = null;
        if ($request->filled('stall')) {
            $selectedStall = \App\Models\CanteenStall::where('slug', $request->stall)->first();
            if ($selectedStall) {
                $query->where('stall_id', $selectedStall->id);
            }
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('promo') && $request->promo == '1') {
            $query->where('discount_percent', '>', 0);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('stall_name', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(8)->withQueryString();
        $student = auth()->user()->student;

        $recentOrders = [];
        if ($student) {
            $recentOrders = CanteenOrder::where('student_id', $student->id)
                ->latest()
                ->take(5)
                ->get();
        }

        $promoBanners = [
            [
                'title' => 'Diskon Hemat E-Kantin Up to 25%',
                'subtitle' => 'Nikmati promo potongan harga makanan & minuman favoritmu hari ini!',
                'badge' => 'PROMO DISKON',
                'bg_gradient' => 'from-indigo-600 via-indigo-700 to-slate-800',
                'btn_text' => 'Cek Menu Promo',
                'filter' => 'promo',
            ],
            [
                'title' => 'Bayar Praktis Pakai Saldo Tabungan',
                'subtitle' => 'Cashless, tanpa antri uang pas, dan transaksi langsung lunas!',
                'badge' => 'CASHLESS WALLET',
                'bg_gradient' => 'from-slate-800 via-indigo-900 to-slate-900',
                'btn_text' => 'Saldo Tabungan',
                'filter' => 'savings',
            ],
            [
                'title' => 'QR Code Payment Hub Pertransaksi',
                'subtitle' => 'Gunakan tombol Scan QR di navigasi tengah untuk pembayaran cepat.',
                'badge' => 'SCAN & PAY',
                'bg_gradient' => 'from-indigo-700 via-slate-800 to-indigo-950',
                'btn_text' => 'Scan QR',
                'filter' => 'qr',
            ],
        ];

        return view('student.canteen.index', compact('categories', 'stalls', 'selectedStall', 'items', 'student', 'recentOrders', 'promoBanners'));
    }

    /**
     * Display Canteen Stall/Vendor Profile (Shopee-style Store Front).
     */
    public function stallDetail(Request $request, $slug)
    {
        $stall = \App\Models\CanteenStall::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = CanteenItem::where('stall_id', $stall->id)->where('is_available', true)->with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('promo') && $request->promo == '1') {
            $query->where('discount_percent', '>', 0);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $items = $query->latest()->paginate(6)->withQueryString();
        $student = auth()->user()->student;

        // Categories available inside this specific stall
        $stallCategoryIds = CanteenItem::where('stall_id', $stall->id)->where('is_available', true)->pluck('category_id')->unique();
        $categories = CanteenCategory::whereIn('id', $stallCategoryIds)->get();

        return view('student.canteen.stall', compact('stall', 'items', 'categories', 'student'));
    }

    /**
     * Process Marketplace Checkout.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:canteen_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:savings_balance,qris,cash',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($request->items as $cartItem) {
                $item = CanteenItem::lockForUpdate()->find($cartItem['id']);

                if (!$item || !$item->is_available || $item->stock < $cartItem['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok produk ' . ($item ? $item->name : '') . ' tidak mencukupi atau tidak tersedia.'
                    ], 422);
                }

                $subtotal = $item->price * $cartItem['qty'];
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'canteen_item_id' => $item->id,
                    'item_name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $cartItem['qty'],
                    'subtotal' => $subtotal,
                ];

                // Deduct stock
                $item->decrement('stock', $cartItem['qty']);
            }

            $paymentMethod = $request->payment_method;
            $paymentStatus = 'unpaid';
            $orderStatus = 'pending';
            $paidAt = null;
            $orderNumber = 'KTN-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Check if savings balance is sufficient before placing unpaid order
            if ($paymentMethod === 'savings_balance') {
                if ($student->savings_balance < $totalAmount) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Saldo QRpay Anda tidak mencukupi (Saldo Anda: Rp ' . number_format($student->savings_balance, 0, ',', '.') . ').'
                    ], 422);
                }
            }

            $order = CanteenOrder::create([
                'order_number' => $orderNumber,
                'student_id' => $student->id,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'qr_code' => 'KTNQR-' . date('YmdHis') . '-' . strtoupper(Str::random(8)),
                'paid_at' => $paidAt,
                'notes' => $request->notes,
            ]);

            foreach ($orderItemsData as $itemData) {
                $itemData['canteen_order_id'] = $order->id;
                CanteenOrderItem::create($itemData);
            }

            // Add balance to canteen stall if paid instantly
            if ($paymentMethod === 'savings_balance' && $paymentStatus === 'paid') {
                $firstItemData = $orderItemsData[0] ?? null;
                if ($firstItemData) {
                    $itemModel = CanteenItem::find($firstItemData['canteen_item_id']);
                    if ($itemModel && $itemModel->stall_id) {
                        $stall = CanteenStall::find($itemModel->stall_id);
                        if ($stall) {
                            $stall->increment('balance', $totalAmount);
                            
                            CanteenTransaction::create([
                                'stall_id' => $stall->id,
                                'type' => 'income',
                                'amount' => $totalAmount,
                                'balance_after' => $stall->balance,
                                'reference_no' => $order->order_number,
                                'description' => 'Pembayaran QRpay pesanan ' . $order->order_number,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'redirect_url' => route('student.canteen.order', $order->id),
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Order Detail & Per-Transaction QR Code.
     */
    public function orderDetail($id)
    {
        $student = auth()->user()->student;

        $order = CanteenOrder::where('id', $id)
            ->where('student_id', $student->id)
            ->with(['items.item', 'student.user', 'cashier'])
            ->firstOrFail();

        return view('student.canteen.order-detail', compact('order', 'student'));
    }

    /**
     * List all student orders history.
     */
    public function myOrders()
    {
        $student = auth()->user()->student;

        $orders = CanteenOrder::where('student_id', $student->id)
            ->with(['items'])
            ->latest()
            ->paginate(10);

        return view('student.canteen.orders', compact('orders', 'student'));
    }

    /**
     * Pay a pending order with savings balance.
     */
    public function payWithSavings(Request $request, $id)
    {
        $student = auth()->user()->student;

        $request->validate([
            'pin' => 'required|numeric|digits:6',
        ], [
            'pin.required' => 'PIN keamanan wajib diisi untuk melakukan pembayaran.',
            'pin.numeric' => 'PIN harus berupa angka.',
            'pin.digits' => 'PIN harus terdiri dari 6 digit.',
        ]);

        if (empty($student->pin)) {
            return back()->with('error', 'PIN keamanan Anda belum diatur. Silakan buat PIN terlebih dahulu di bagian akun/profil.');
        }

        if (!Hash::check($request->pin, $student->pin)) {
            return back()->with('error', 'PIN keamanan yang Anda masukkan salah.');
        }

        $order = CanteenOrder::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Pesanan ini sudah lunas.');
        }

        if ($student->savings_balance < $order->total_amount) {
            return back()->with('error', 'Saldo tabungan Anda tidak mencukupi untuk membayar pesanan ini.');
        }

        DB::beginTransaction();
        try {
            $student->decrement('savings_balance', $order->total_amount);

            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'payment_method' => 'savings_balance',
                'paid_at' => now(),
            ]);

            if (Schema::hasTable('savings_transactions')) {
                DB::table('savings_transactions')->insert([
                    'student_id'       => $student->id,
                    'transaction_type' => 'withdraw',
                    'amount'           => $order->total_amount,
                    'balance_after'    => $student->savings_balance,
                    'reference_no'     => $order->order_number,
                    'notes'            => 'Pembayaran Kantin Order ' . $order->order_number,
                    'created_by'       => auth()->id(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // Add balance to canteen stall
            $firstItem = $order->items()->first();
            if ($firstItem && $firstItem->item && $firstItem->item->stall_id) {
                $stall = CanteenStall::find($firstItem->item->stall_id);
                if ($stall) {
                    $stall->increment('balance', $order->total_amount);
                    
                    CanteenTransaction::create([
                        'stall_id' => $stall->id,
                        'type' => 'income',
                        'amount' => $order->total_amount,
                        'balance_after' => $stall->balance,
                        'reference_no' => $order->order_number,
                        'description' => 'Pembayaran QRpay pesanan ' . $order->order_number,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Pembayaran menggunakan Saldo Tabungan berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Process scanned QR Code payload from center navigation QR scanner.
     */
    public function processQrScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $qrData = trim($request->qr_data);
        $student = auth()->user()->student;

        // 1. If scanned Canteen Order QR Code
        if (Str::startsWith($qrData, 'KTNQR-')) {
            $order = CanteenOrder::where('qr_code', $qrData)
                ->with(['items', 'student.user'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'type'    => 'canteen_order',
                    'message' => 'Pesanan kantin tidak ditemukan untuk Kode QR tersebut.'
                ]);
            }

            // Pastikan pesanan ini milik siswa yang sedang login atau merupakan pesanan POS umum (student_id === null)
            if ($order->student_id !== null && $order->student_id !== $student->id) {
                return response()->json([
                    'success' => false,
                    'type'    => 'canteen_order',
                    'message' => 'QR Code ini bukan milik akun Anda. Silakan scan QR pesanan Anda sendiri.'
                ]);
            }

            // Jika pesanan POS belum diklaim (student_id masih null), klaim atas nama siswa ini
            if ($order->student_id === null) {
                $order->student_id = $student->id;
                $order->save();
            }

            return response()->json([
                'success'      => true,
                'type'         => 'canteen_order',
                'redirect_url' => route('student.canteen.order', $order->id),
                'order'        => [
                    'id'             => $order->id,
                    'order_number'   => $order->order_number,
                    'total_amount'   => $order->total_amount,
                    'total_formatted'=> 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    'payment_status' => $order->payment_status,
                    'order_status'   => $order->order_status,
                    'items_count'    => $order->items->count(),
                    'can_pay'        => $order->payment_status !== 'paid' && $student->savings_balance >= $order->total_amount,
                ]
            ]);
        }

        // 2. If scanned Student QR Code
        if (Str::startsWith($qrData, 'STD-') || Str::startsWith($qrData, '{"student_id"')) {
            $scannedStudent = null;
            if (Str::startsWith($qrData, '{')) {
                $decoded = json_decode($qrData, true);
                if (isset($decoded['qr_code'])) {
                    $scannedStudent = Student::where('qr_code', $decoded['qr_code'])->with('user', 'class')->first();
                }
            } else {
                $scannedStudent = Student::where('qr_code', $qrData)->with('user', 'class')->first();
            }

            if ($scannedStudent) {
                return response()->json([
                    'success' => true,
                    'type' => 'student_card',
                    'student' => [
                        'name' => $scannedStudent->user?->name,
                        'nisn' => $scannedStudent->nisn,
                        'class' => $scannedStudent->class?->name ?? '-',
                        'qr_code' => $scannedStudent->qr_code,
                    ]
                ]);
            }
        }

        // 3. Fallback generic QR payload handling (for future general payment features)
        return response()->json([
            'success' => true,
            'type' => 'general',
            'qr_data' => $qrData,
            'message' => 'QR Code berhasil dipindai: ' . $qrData,
        ]);
    }
}
