<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\CanteenCategory;
use App\Models\CanteenItem;
use App\Models\CanteenOrder;
use App\Models\CanteenOrderItem;
use App\Models\CanteenStall;
use App\Models\CanteenWithdrawal;
use App\Models\CanteenTransaction;
use App\Models\SavingsTransaction;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorCanteenController extends Controller
{
    /**
     * Helper to get active vendor stall.
     */
    private function getVendorStall()
    {
        $user = auth()->user();
        
        // If stall linked by name or default first stall
        $stall = CanteenStall::where('owner_name', $user->name)->first();
        if (!$stall) {
            $stall = CanteenStall::first();
        }

        if (!$stall) {
            $stall = CanteenStall::create([
                'name' => 'Kantin Stand Utama',
                'slug' => 'kantin-stand-utama',
                'owner_name' => $user->name,
                'open_time' => '07:00',
                'close_time' => '15:00',
                'operating_hours' => '07:00 - 15:00 WIB',
                'description' => 'Stand Kantin Sehat Sekolah',
                'rating' => 4.8,
                'is_active' => true,
            ]);
        }

        return $stall;
    }

    /**
     * Vendor Mobile & Desktop Dashboard.
     */
    public function dashboard()
    {
        $stall = $this->getVendorStall();

        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');
        
        $todayOrders = CanteenOrder::whereDate('created_at', $today)->latest()->get();
        $todayRevenue = $todayOrders->where('payment_status', 'paid')->sum('total_amount');
        
        $monthlyRevenue = CanteenOrder::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $pendingOrdersCount = $todayOrders->whereIn('order_status', ['pending', 'processing', 'ready'])->count();
        $totalProductsCount = CanteenItem::where('stall_id', $stall->id)->count();

        $statusCounts = [
            'pending' => CanteenOrder::where('order_status', 'pending')->count(),
            'processing' => CanteenOrder::where('order_status', 'processing')->count(),
            'ready' => CanteenOrder::where('order_status', 'ready')->count(),
            'completed' => CanteenOrder::whereDate('created_at', $today)->where('order_status', 'completed')->count(),
        ];

        $recentOrders = CanteenOrder::with(['items', 'student.user', 'student.class'])
            ->latest()
            ->take(8)
            ->get();

        return view('canteen-vendor.dashboard', compact(
            'stall', 
            'todayRevenue', 
            'monthlyRevenue',
            'pendingOrdersCount', 
            'totalProductsCount', 
            'recentOrders',
            'statusCounts'
        ));
    }

    /**
     * Toggle Store Active Status (Buka / Tutup).
     */
    public function toggleStatus(Request $request)
    {
        $stall = $this->getVendorStall();
        $stall->is_active = $request->has('is_active') ? (bool)$request->is_active : !$stall->is_active;
        $stall->save();

        $statusText = $stall->is_active ? 'Buka (Menerima Pesanan)' : 'Tutup Sementara';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $stall->is_active,
                'message' => 'Status stand berhasil diubah menjadi: ' . $statusText,
            ]);
        }

        return redirect()->back()->with('success', 'Status toko berhasil diubah menjadi ' . $statusText);
    }

    /**
     * Vendor Orders Management List.
     */
    public function orders(Request $request)
    {
        $stall = $this->getVendorStall();
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $dateFilter = $request->get('date', 'all');

        $query = CanteenOrder::with(['items', 'student.user', 'student.class'])->latest();

        if ($status !== 'all') {
            $query->where('order_status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('qr_code', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFilter === 'today') {
            $query->whereDate('created_at', now()->format('Y-m-d'));
        } elseif ($dateFilter === 'yesterday') {
            $query->whereDate('created_at', now()->subDay()->format('Y-m-d'));
        } elseif ($dateFilter === 'this_month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        $orders = $query->paginate(12)->withQueryString();

        $statusCounts = [
            'all' => CanteenOrder::count(),
            'pending' => CanteenOrder::where('order_status', 'pending')->count(),
            'processing' => CanteenOrder::where('order_status', 'processing')->count(),
            'ready' => CanteenOrder::where('order_status', 'ready')->count(),
            'completed' => CanteenOrder::where('order_status', 'completed')->count(),
            'cancelled' => CanteenOrder::where('order_status', 'cancelled')->count(),
        ];

        return view('canteen-vendor.orders', compact('stall', 'orders', 'status', 'search', 'dateFilter', 'statusCounts'));
    }

    /**
     * Update Order Status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,ready,completed,cancelled',
        ]);

        $order = CanteenOrder::findOrFail($id);
        $order->order_status = $request->order_status;

        if ($request->order_status === 'completed' && $order->payment_status !== 'paid') {
            $order->payment_status = 'paid';
            $order->paid_at = now();
        }

        $order->save();

        return redirect()->back()->with('success', 'Status pesanan #' . $order->order_number . ' berhasil diperbarui.');
    }

    /**
     * Vendor Products Catalog Management.
     */
    public function products(Request $request)
    {
        $stall = $this->getVendorStall();
        $categories = CanteenCategory::where('is_active', true)->get();
        
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $query = CanteenItem::where('stall_id', $stall->id)->with('category');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('canteen-vendor.products', compact('stall', 'categories', 'products', 'search', 'categoryId'));
    }

    /**
     * Store New Product.
     */
    public function storeProduct(Request $request)
    {
        $stall = $this->getVendorStall();

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:canteen_categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $savedImage = save_uploaded_public_file($request->file('image'), 'img/canteen/items');
            $imagePath = 'canteen/items/' . basename($savedImage);
        }

        CanteenItem::create([
            'stall_id' => $stall->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'price' => $request->price,
            'original_price' => $request->original_price,
            'discount_percent' => $request->discount_percent ?? 0,
            'stock' => $request->stock,
            'stall_name' => $stall->name,
            'description' => $request->description,
            'image' => $imagePath,
            'is_available' => $request->stock > 0,
        ]);

        return redirect()->back()->with('success', 'Produk menu baru berhasil ditambahkan!');
    }

    /**
     * Update Product.
     */
    public function updateProduct(Request $request, $id)
    {
        $product = CanteenItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:canteen_categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                delete_public_file('img/' . ltrim($product->image, '/'));
            }
            $savedImage = save_uploaded_public_file($request->file('image'), 'img/canteen/items');
            $product->image = 'canteen/items/' . basename($savedImage);
        }

        $product->name = $request->name;
        $product->slug = Str::slug($request->name) . '-' . time();
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->original_price = $request->original_price;
        $product->discount_percent = $request->discount_percent ?? 0;
        $product->stock = $request->stock;
        $product->description = $request->description;
        $product->is_available = $request->stock > 0;
        $product->save();

        return redirect()->back()->with('success', 'Menu produk berhasil diperbarui.');
    }

    /**
     * Delete Product.
     */
    public function destroyProduct($id)
    {
        $product = CanteenItem::findOrFail($id);
        if ($product->image) {
            delete_public_file($product->image, 'img/canteen/items');
        }
        $product->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Vendor Stall Profile Management.
     */
    public function profile()
    {
        $stall = $this->getVendorStall();
        $totalItems = CanteenItem::where('stall_id', $stall->id)->count();
        $totalCompletedOrders = CanteenOrder::where('order_status', 'completed')->count();

        return view('canteen-vendor.profile', compact('stall', 'totalItems', 'totalCompletedOrders'));
    }

    /**
     * Update Stall Profile.
     */
    public function updateProfile(Request $request)
    {
        $stall = $this->getVendorStall();

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'open_time' => 'nullable|string',
            'close_time' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:3072',
            'is_active' => 'required|boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($stall->logo) {
                delete_public_file('img/' . ltrim($stall->logo, '/'));
            }
            $savedLogo = save_uploaded_public_file($request->file('logo'), 'img/canteen/stalls');
            $stall->logo = 'canteen/stalls/' . basename($savedLogo);
        }

        if ($request->hasFile('banner')) {
            if ($stall->banner) {
                delete_public_file('img/' . ltrim($stall->banner, '/'));
            }
            $savedBanner = save_uploaded_public_file($request->file('banner'), 'img/canteen/stalls');
            $stall->banner = 'canteen/stalls/' . basename($savedBanner);
        }

        $openTime = $request->open_time ?? '07:00';
        $closeTime = $request->close_time ?? '15:00';

        $stall->name = $request->name;
        $stall->slug = Str::slug($request->name);
        $stall->owner_name = $request->owner_name;
        $stall->phone = $request->phone;
        $stall->open_time = $openTime;
        $stall->close_time = $closeTime;
        $stall->operating_hours = $openTime . ' - ' . $closeTime . ' WIB';
        $stall->description = $request->description;
        $stall->is_active = (bool)$request->is_active;
        $stall->save();

        return redirect()->back()->with('success', 'Profil toko kantin berhasil diperbarui.');
    }

    /**
     * QR Scanner Page.
     */
    public function scanQr()
    {
        $stall = $this->getVendorStall();
        return view('canteen-vendor.scan-qr', compact('stall'));
    }

    /**
     * Verify QR Code or Order Number or Student KTS (API Endpoint for Scanner).
     */
    public function verifyQrScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $queryStr = trim($request->qr_data);

        // 1. Try finding Canteen Order first
        $order = CanteenOrder::with(['items', 'student.user', 'student.class'])
            ->where(function ($q) use ($queryStr) {
                $q->where('qr_code', $queryStr)
                  ->orWhere('order_number', $queryStr)
                  ->orWhere('id', $queryStr);
            })
            ->first();

        if ($order) {
            return response()->json([
                'success' => true,
                'type' => 'order',
                'message' => 'Pesanan ditemukan!',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'qr_code' => $order->qr_code,
                    'student_name' => $order->student?->user?->name ?? 'Siswa',
                    'student_nisn' => $order->student?->nisn ?? '-',
                    'student_class' => $order->student?->class?->name ?? 'Kelas -',
                    'total_amount' => $order->total_amount,
                    'total_formatted' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    'payment_method' => $order->payment_method === 'savings_balance' ? 'Saldo Tabungan' : strtoupper($order->payment_method ?? 'TUNAI'),
                    'payment_status' => $order->payment_status,
                    'payment_status_badge' => $order->payment_status_badge,
                    'order_status' => $order->order_status,
                    'order_status_badge' => $order->order_status_badge,
                    'created_at_formatted' => $order->created_at->translatedFormat('d M Y, H:i') . ' WIB',
                    'notes' => $order->notes ?? '',
                    'items' => $order->items->map(function ($item) {
                        return [
                            'item_name' => $item->item_name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'price_formatted' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                            'subtotal' => $item->subtotal,
                            'subtotal_formatted' => 'Rp ' . number_format($item->subtotal, 0, ',', '.'),
                        ];
                    }),
                ]
            ]);
        }

        // 2. Try finding Student KTS Card
        $student = null;
        if (Str::startsWith($queryStr, '{') && Str::endsWith($queryStr, '}')) {
            $decoded = json_decode($queryStr, true);
            if (is_array($decoded)) {
                if (!empty($decoded['student_id'])) {
                    $student = Student::with(['user', 'schoolClass'])->find($decoded['student_id']);
                } elseif (!empty($decoded['qr_code'])) {
                    $student = Student::with(['user', 'schoolClass'])->where('qr_code', $decoded['qr_code'])->first();
                } elseif (!empty($decoded['nisn'])) {
                    $student = Student::with(['user', 'schoolClass'])->where('nisn', $decoded['nisn'])->first();
                }
            }
        }

        if (!$student) {
            $student = Student::with(['user', 'schoolClass'])
                ->where(function ($q) use ($queryStr) {
                    $q->where('qr_code', $queryStr)
                      ->orWhere('nisn', $queryStr)
                      ->orWhere('id', $queryStr);
                })
                ->first();
        }

        if ($student) {
            return response()->json([
                'success' => true,
                'type' => 'student',
                'message' => 'Kartu Tanda Siswa Ditemukan!',
                'student' => [
                    'id' => $student->id,
                    'name' => $student->user?->name ?? 'Siswa',
                    'nisn' => $student->nisn ?? '-',
                    'class' => $student->schoolClass?->name ?? 'Kelas -',
                    'photo_url' => $student->photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($student->user?->name ?? 'Siswa') . '&background=6366f1&color=fff'),
                    'savings_balance' => (float)$student->savings_balance,
                    'savings_balance_formatted' => 'Rp ' . number_format($student->savings_balance, 0, ',', '.'),
                    'has_pin' => !empty($student->pin),
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Pesanan atau Kartu Siswa tidak ditemukan untuk QR Code: ' . $queryStr,
        ], 404);
    }

    /**
     * Vendor Categories List & Management.
     */
    public function categories(Request $request)
    {
        $stall = $this->getVendorStall();
        $search = $request->get('search');

        $query = CanteenCategory::withCount('items');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->latest()->paginate(10)->withQueryString();

        return view('canteen-vendor.categories', compact('stall', 'categories', 'search'));
    }

    /**
     * Store New Category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:canteen_categories,name',
            'icon' => 'nullable|string|max:100',
        ]);

        CanteenCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? 'utensils',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Kategori menu baru berhasil ditambahkan!');
    }

    /**
     * Update Category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = CanteenCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:canteen_categories,name,' . $id,
            'icon' => 'nullable|string|max:100',
        ]);

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->has('icon')) {
            $category->icon = $request->icon;
        }
        $category->save();

        return redirect()->back()->with('success', 'Kategori menu berhasil diperbarui.');
    }

    /**
     * Toggle Category Active Status.
     */
    public function toggleCategory(Request $request, $id)
    {
        $category = CanteenCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return redirect()->back()->with('success', 'Status kategori ' . $category->name . ' berhasil diubah.');
    }

    /**
     * Delete Category.
     */
    public function destroyCategory($id)
    {
        $category = CanteenCategory::findOrFail($id);
        
        if ($category->items()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus kategori yang masih memiliki menu produk.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Stock Management Page.
     */
    public function stock(Request $request)
    {
        $stall = $this->getVendorStall();
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = CanteenItem::where('stall_id', $stall->id)->with('category');

        if ($status === 'low_stock') {
            $query->where('stock', '>', 0)->where('stock', '<=', 5);
        } elseif ($status === 'out_of_stock') {
            $query->where('stock', 0);
        }

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->orderBy('stock', 'asc')->paginate(15)->withQueryString();

        $stockSummary = [
            'total' => CanteenItem::where('stall_id', $stall->id)->count(),
            'low_stock' => CanteenItem::where('stall_id', $stall->id)->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
            'out_of_stock' => CanteenItem::where('stall_id', $stall->id)->where('stock', 0)->count(),
        ];

        return view('canteen-vendor.stock', compact('stall', 'items', 'status', 'search', 'stockSummary'));
    }

    /**
     * Quick Single Item Stock Update.
     */
    public function quickUpdateStock(Request $request, $id)
    {
        $item = CanteenItem::findOrFail($id);

        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $item->stock = $request->stock;
        $item->is_available = $request->stock > 0;
        $item->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'stock' => $item->stock,
                'is_available' => $item->is_available,
                'message' => 'Stok ' . $item->name . ' berhasil diperbarui menjadi ' . $item->stock,
            ]);
        }

        return redirect()->back()->with('success', 'Stok ' . $item->name . ' berhasil diperbarui.');
    }

    /**
     * Batch Stock Update.
     */
    public function updateStockBatch(Request $request)
    {
        $request->validate([
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0',
        ]);

        foreach ($request->stocks as $itemId => $newStock) {
            $item = CanteenItem::find($itemId);
            if ($item) {
                $item->stock = (int)$newStock;
                $item->is_available = (int)$newStock > 0;
                $item->save();
            }
        }

        return redirect()->back()->with('success', 'Seluruh perubahan stok berhasil disimpan!');
    }

    /**
     * Vendor Sales Reports & Analytics.
     */
    public function reports(Request $request)
    {
        $stall = $this->getVendorStall();
        $period = $request->get('period', 'daily'); // 'daily', 'weekly', 'monthly', 'custom'
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Determine date range based on period
        if ($period === 'daily') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->startOfDay();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfDay() : now()->endOfDay();
        } elseif ($period === 'weekly') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfWeek() : now()->startOfWeek();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfWeek() : now()->endOfWeek();
        } elseif ($period === 'monthly') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfMonth() : now()->startOfMonth();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfMonth() : now()->endOfMonth();
        } else { // 'custom'
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->subDays(7)->startOfDay();
            $end = !empty($endDate) ? \Carbon\Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
        }

        // Base Query for Completed Orders in Period
        $ordersQuery = CanteenOrder::where('order_status', 'completed')
            ->whereBetween('created_at', [$start, $end]);

        $totalRevenue = (float) (clone $ordersQuery)->sum('total_amount');
        $totalOrders = (int) (clone $ordersQuery)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Items sold count
        $orderIds = (clone $ordersQuery)->pluck('id');
        $totalItemsSold = (int) CanteenOrderItem::whereIn('canteen_order_id', $orderIds)->sum('quantity');

        // Top Selling Items in Period
        $topSellingItems = CanteenOrderItem::whereIn('canteen_order_id', $orderIds)
            ->selectRaw('item_name, SUM(quantity) as total_qty, SUM(subtotal) as total_sales')
            ->groupBy('item_name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // Payment Method Breakdown
        $paymentBreakdown = [
            'savings_balance' => (float) (clone $ordersQuery)->where('payment_method', 'savings_balance')->sum('total_amount'),
            'cash' => (float) (clone $ordersQuery)->whereIn('payment_method', ['cash', 'tunai'])->sum('total_amount'),
            'other' => (float) (clone $ordersQuery)->whereNotIn('payment_method', ['savings_balance', 'cash', 'tunai'])->sum('total_amount'),
        ];

        // Chart Data Generator
        $chartLabels = [];
        $chartRevenueData = [];
        $chartOrderData = [];

        if ($period === 'daily') {
            for ($hour = 7; $hour <= 17; $hour++) {
                $hourStr = sprintf('%02d:00', $hour);
                $chartLabels[] = $hourStr;
                
                $hourStart = (clone $start)->hour($hour)->minute(0)->second(0);
                $hourEnd = (clone $start)->hour($hour)->minute(59)->second(59);

                $hRevenue = (float) CanteenOrder::where('order_status', 'completed')
                    ->whereBetween('created_at', [$hourStart, $hourEnd])->sum('total_amount');
                $hCount = (int) CanteenOrder::where('order_status', 'completed')
                    ->whereBetween('created_at', [$hourStart, $hourEnd])->count();

                $chartRevenueData[] = $hRevenue;
                $chartOrderData[] = $hCount;
            }
        } else {
            $periodDays = $start->diffInDays($end) + 1;
            if ($periodDays > 31) $periodDays = 31;

            for ($i = 0; $i < $periodDays; $i++) {
                $currentDay = (clone $start)->addDays($i);
                $chartLabels[] = $currentDay->translatedFormat('d M');

                $dayRevenue = (float) CanteenOrder::where('order_status', 'completed')
                    ->whereDate('created_at', $currentDay->format('Y-m-d'))->sum('total_amount');
                $dayCount = (int) CanteenOrder::where('order_status', 'completed')
                    ->whereDate('created_at', $currentDay->format('Y-m-d'))->count();

                $chartRevenueData[] = $dayRevenue;
                $chartOrderData[] = $dayCount;
            }
        }

        // Detailed Recent Orders in Report Period
        $reportOrders = (clone $ordersQuery)->with(['student.user', 'items'])->latest()->paginate(10)->withQueryString();

        return view('canteen-vendor.reports', compact(
            'stall',
            'period',
            'startDate',
            'endDate',
            'start',
            'end',
            'totalRevenue',
            'totalOrders',
            'totalItemsSold',
            'avgOrderValue',
            'topSellingItems',
            'paymentBreakdown',
            'chartLabels',
            'chartRevenueData',
            'chartOrderData',
            'reportOrders'
        ));
    }

    /**
     * Print Canteen Vendor Sales Reports.
     */
    public function printReports(Request $request)
    {
        $stall = $this->getVendorStall();
        $period = $request->get('period', 'daily');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'daily') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->startOfDay();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfDay() : now()->endOfDay();
        } elseif ($period === 'weekly') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfWeek() : now()->startOfWeek();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfWeek() : now()->endOfWeek();
        } elseif ($period === 'monthly') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfMonth() : now()->startOfMonth();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfMonth() : now()->endOfMonth();
        } else {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->subDays(7)->startOfDay();
            $end = !empty($endDate) ? \Carbon\Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
        }

        $ordersQuery = CanteenOrder::where('order_status', 'completed')
            ->whereBetween('created_at', [$start, $end]);

        $totalRevenue = (float) (clone $ordersQuery)->sum('total_amount');
        $totalOrders = (int) (clone $ordersQuery)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $orderIds = (clone $ordersQuery)->pluck('id');
        $totalItemsSold = (int) CanteenOrderItem::whereIn('canteen_order_id', $orderIds)->sum('quantity');

        $topSellingItems = CanteenOrderItem::whereIn('canteen_order_id', $orderIds)
            ->selectRaw('item_name, SUM(quantity) as total_qty, SUM(subtotal) as total_sales')
            ->groupBy('item_name')
            ->orderBy('total_qty', 'desc')
            ->take(10)
            ->get();

        $paymentBreakdown = [
            'savings_balance' => (float) (clone $ordersQuery)->where('payment_method', 'savings_balance')->sum('total_amount'),
            'cash' => (float) (clone $ordersQuery)->whereIn('payment_method', ['cash', 'tunai'])->sum('total_amount'),
            'other' => (float) (clone $ordersQuery)->whereNotIn('payment_method', ['savings_balance', 'cash', 'tunai'])->sum('total_amount'),
        ];

        $reportOrders = (clone $ordersQuery)->with(['student.user', 'items'])->latest()->get();

        return view('canteen-vendor.print-report', compact(
            'stall',
            'period',
            'startDate',
            'endDate',
            'start',
            'end',
            'totalRevenue',
            'totalOrders',
            'totalItemsSold',
            'avgOrderValue',
            'topSellingItems',
            'paymentBreakdown',
            'reportOrders'
        ));
    }

    /**
     * Export Reports Data to CSV.
     */
    public function exportReportsCsv(Request $request)
    {
        $period = $request->get('period', 'daily');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'daily') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->startOfDay();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfDay() : now()->endOfDay();
        } elseif ($period === 'weekly') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfWeek() : now()->startOfWeek();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfWeek() : now()->endOfWeek();
        } elseif ($period === 'monthly') {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfMonth() : now()->startOfMonth();
            $end = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->endOfMonth() : now()->endOfMonth();
        } else {
            $start = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->startOfDay() : now()->subDays(7)->startOfDay();
            $end = !empty($endDate) ? \Carbon\Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
        }

        $orders = CanteenOrder::where('order_status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->with(['student.user', 'items'])
            ->latest()
            ->get();

        $filename = 'Laporan_Penjualan_Kantin_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'No. Pesanan',
                'Tanggal & Waktu',
                'Nama Siswa',
                'Metode Pembayaran',
                'Detail Items',
                'Total Pembayaran (Rp)',
                'Status'
            ]);

            foreach ($orders as $order) {
                $itemList = $order->items->map(fn($item) => $item->item_name . ' (' . $item->quantity . 'x)')->implode(', ');
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->student?->user?->name ?? 'Siswa',
                    $order->payment_method === 'savings_balance' ? 'Saldo Tabungan' : strtoupper($order->payment_method ?? 'TUNAI'),
                    $itemList,
                    $order->total_amount,
                    'Selesai'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Vendor POS (Point of Sale / Kasir Kantin) Page.
     */
    public function pos(Request $request)
    {
        $stall = $this->getVendorStall();
        $categories = CanteenCategory::where('is_active', true)->get();
        
        $products = CanteenItem::where('stall_id', $stall->id)
            ->where('is_available', true)
            ->with('category')
            ->orderBy('name', 'asc')
            ->get();

        return view('canteen-vendor.pos', compact('stall', 'categories', 'products'));
    }

    /**
     * Search Students for POS.
     */
    public function searchStudents(Request $request)
    {
        $queryStr = trim($request->get('q', ''));

        if (empty($queryStr)) {
            return response()->json([]);
        }

        $students = Student::with(['user', 'schoolClass'])
            ->where(function ($q) use ($queryStr) {
                $q->where('nisn', 'like', "%{$queryStr}%")
                  ->orWhere('qr_code', $queryStr)
                  ->orWhereHas('user', function ($uq) use ($queryStr) {
                      $uq->where('name', 'like', "%{$queryStr}%");
                  });
            })
            ->take(10)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user?->name ?? 'Siswa',
                    'nisn' => $student->nisn,
                    'class_name' => $student->schoolClass?->name ?? 'Kelas -',
                    'savings_balance' => (float)$student->savings_balance,
                    'savings_balance_formatted' => 'Rp ' . number_format($student->savings_balance, 0, ',', '.'),
                    'photo_url' => $student->photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($student->user?->name ?? 'Siswa') . '&background=6366f1&color=fff'),
                    'has_pin' => !empty($student->pin),
                ];
            });

        return response()->json($students);
    }

    /**
     * Lookup Student for POS Tabungan / KTS Payment.
     */
    public function lookupStudentForPayment(Request $request)
    {
        $qrData = trim($request->get('qr_data', ''));
        $studentId = $request->get('student_id');

        $student = null;

        if (!empty($studentId)) {
            $student = Student::with(['user', 'schoolClass'])->find($studentId);
        }

        if (!$student && !empty($qrData)) {
            if (Str::startsWith($qrData, '{') && Str::endsWith($qrData, '}')) {
                $decoded = json_decode($qrData, true);
                if (is_array($decoded)) {
                    if (!empty($decoded['student_id'])) {
                        $student = Student::with(['user', 'schoolClass'])->find($decoded['student_id']);
                    } elseif (!empty($decoded['qr_code'])) {
                        $student = Student::with(['user', 'schoolClass'])->where('qr_code', $decoded['qr_code'])->first();
                    } elseif (!empty($decoded['nisn'])) {
                        $student = Student::with(['user', 'schoolClass'])->where('nisn', $decoded['nisn'])->first();
                    }
                }
            }

            if (!$student) {
                $student = Student::with(['user', 'schoolClass'])
                    ->where(function ($q) use ($qrData) {
                        $q->where('qr_code', $qrData)
                          ->orWhere('nisn', $qrData)
                          ->orWhere('id', $qrData)
                          ->orWhereHas('user', function ($uq) use ($qrData) {
                              $uq->where('name', $qrData);
                          });
                    })
                    ->first();
            }
        }

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data QR / Siswa tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Siswa ditemukan.',
            'student' => [
                'id' => $student->id,
                'name' => $student->user?->name ?? 'Siswa',
                'nisn' => $student->nisn ?? '-',
                'class_name' => $student->schoolClass?->name ?? 'Kelas -',
                'photo_url' => $student->photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($student->user?->name ?? 'Siswa') . '&background=6366f1&color=fff'),
                'savings_balance' => (float)$student->savings_balance,
                'savings_balance_formatted' => 'Rp ' . number_format($student->savings_balance, 0, ',', '.'),
                'has_pin' => !empty($student->pin),
            ]
        ]);
    }

    /**
     * Process POS Transaction Checkout (Supports 'cash', 'qris', and 'savings_balance').
     */
    public function posCheckout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:canteen_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris,savings_balance',
            'paid_amount' => 'nullable|numeric|min:0',
            'student_id' => 'nullable|exists:students,id',
            'pin' => 'nullable|string',
        ]);

        $stall = $this->getVendorStall();

        return DB::transaction(function () use ($request, $stall) {
            $cartItems = $request->items;
            $paymentMethod = $request->payment_method;
            $paidAmount = (float)($request->paid_amount ?? 0);
            $student = null;

            $itemIds = array_column($cartItems, 'id');
            $itemsData = CanteenItem::whereIn('id', $itemIds)->where('stall_id', $stall->id)->get()->keyBy('id');

            $totalAmount = 0;
            $orderItemsToInsert = [];

            foreach ($cartItems as $cItem) {
                $itemModel = $itemsData->get($cItem['id']);
                if (!$itemModel) {
                    throw new \Exception('Salah satu menu produk tidak ditemukan.');
                }
                if ($itemModel->stock < $cItem['quantity']) {
                    throw new \Exception("Stok {$itemModel->name} tidak mencukupi (Tersisa: {$itemModel->stock}).");
                }

                $subtotal = $itemModel->price * $cItem['quantity'];
                $totalAmount += $subtotal;

                $orderItemsToInsert[] = [
                    'item_model' => $itemModel,
                    'quantity' => $cItem['quantity'],
                    'price' => $itemModel->price,
                    'subtotal' => $subtotal,
                ];
            }

            if ($paymentMethod === 'cash') {
                if ($paidAmount < $totalAmount) {
                    throw new \Exception('Nominal pembayaran tunai kurang dari total tagihan.');
                }
                $paymentStatus = 'paid';
                $orderStatus = 'completed';
                $paidAt = now();
            } elseif ($paymentMethod === 'savings_balance') {
                if (!$request->filled('student_id')) {
                    throw new \Exception('Silakan pilih atau scan KTS siswa terlebih dahulu.');
                }
                if (!$request->filled('pin')) {
                    throw new \Exception('PIN Keamanan Siswa wajib dimasukkan.');
                }

                $student = Student::with('user')->find($request->student_id);
                if (!$student) {
                    throw new \Exception('Data siswa tidak ditemukan.');
                }

                // Verify PIN (Use Hash::check or fallback default 123456 if pin column null)
                $pinInput = (string)$request->pin;
                $isValidPin = false;
                if (!empty($student->pin)) {
                    $isValidPin = Hash::check($pinInput, $student->pin);
                } else {
                    $isValidPin = ($pinInput === '123456');
                }

                if (!$isValidPin) {
                    throw new \Exception('PIN Keamanan Siswa yang dimasukkan tidak cocok/salah.');
                }

                if ($student->savings_balance < $totalAmount) {
                    throw new \Exception("Saldo tabungan siswa tidak mencukupi untuk total tagihan transaksi ini.");
                }

                // Deduct student savings balance
                $student->decrement('savings_balance', $totalAmount);
                $student->refresh();

                // Create Savings Transaction history
                SavingsTransaction::create([
                    'student_id' => $student->id,
                    'transaction_type' => 'payment',
                    'amount' => $totalAmount,
                    'balance_after' => $student->savings_balance,
                    'reference_no' => 'KTN-' . date('YmdHis') . '-' . Str::random(4),
                    'notes' => 'Pembayaran Kantin POS: ' . $stall->name,
                    'created_by' => auth()->id(),
                ]);

                // Increment Stall Balance & Transaction
                $stall->increment('balance', $totalAmount);
                CanteenTransaction::create([
                    'stall_id' => $stall->id,
                    'type' => 'income',
                    'amount' => $totalAmount,
                    'balance_after' => $stall->balance,
                    'reference_no' => 'REV-' . time(),
                    'description' => 'Pendapatan Transaksi Tabungan Siswa: ' . ($student->user?->name ?? 'Siswa'),
                ]);

                $paymentStatus = 'paid';
                $orderStatus = 'completed';
                $paidAt = now();
            } else { // 'qris'
                $paymentStatus = 'unpaid';
                $orderStatus = 'pending';
                $paidAt = null;
            }

            $order = CanteenOrder::create([
                'student_id' => $student?->id,
                'cashier_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'paid_at' => $paidAt,
                'notes' => $paymentMethod === 'savings_balance' ? 'Pembayaran via Scan KTS (Tabungan Siswa + PIN)' : 'Transaksi POS Kasir Vendor',
            ]);

            foreach ($orderItemsToInsert as $oItem) {
                CanteenOrderItem::create([
                    'canteen_order_id' => $order->id,
                    'canteen_item_id' => $oItem['item_model']->id,
                    'item_name' => $oItem['item_model']->name,
                    'price' => $oItem['price'],
                    'quantity' => $oItem['quantity'],
                    'subtotal' => $oItem['subtotal'],
                ]);

                $itemModel = $oItem['item_model'];
                $itemModel->stock -= $oItem['quantity'];
                if ($itemModel->stock <= 0) {
                    $itemModel->stock = 0;
                    $itemModel->is_available = false;
                }
                $itemModel->save();
            }

            $changeAmount = $paymentMethod === 'cash' ? max(0, $paidAmount - $totalAmount) : 0;
            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($order->qr_code);

            $paymentMethodLabel = match($paymentMethod) {
                'savings_balance' => 'Tabungan Siswa',
                'qris' => 'Scan QR Code',
                default => 'Tunai',
            };

            return response()->json([
                'success' => true,
                'message' => $paymentMethod === 'qris' ? 'Kode QR Transaksi Berhasil Dibuat!' : 'Transaksi Pembayaran Selesai!',
                'payment_method' => $paymentMethod,
                'order_id' => $order->id,
                'qr_code' => $order->qr_code,
                'qr_image_url' => $qrApiUrl,
                'receipt' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'date' => $order->created_at->translatedFormat('d M Y, H:i') . ' WIB',
                    'stall_name' => $stall->name,
                    'student_name' => $student ? ($student->user?->name ?? 'Siswa') : 'Pelanggan POS',
                    'student_nisn' => $student ? ($student->nisn ?? '-') : null,
                    'payment_method_label' => $paymentMethodLabel,
                    'total_amount' => $totalAmount,
                    'total_formatted' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
                    'paid_amount' => $paymentMethod === 'cash' ? $paidAmount : $totalAmount,
                    'paid_formatted' => 'Rp ' . number_format($paymentMethod === 'cash' ? $paidAmount : $totalAmount, 0, ',', '.'),
                    'change_amount' => $changeAmount,
                    'change_formatted' => 'Rp ' . number_format($changeAmount, 0, ',', '.'),
                    'items' => array_map(function ($i) {
                        return [
                            'name' => $i['item_model']->name,
                            'qty' => $i['quantity'],
                            'price_formatted' => 'Rp ' . number_format($i['price'], 0, ',', '.'),
                            'subtotal_formatted' => 'Rp ' . number_format($i['subtotal'], 0, ',', '.'),
                        ];
                    }, $orderItemsToInsert)
                ]
            ]);
        });
    }

    /**
     * Check Order Status for POS QR Scanning polling.
     */
    public function checkOrderStatus($id)
    {
        $order = CanteenOrder::find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'is_paid' => $order->payment_status === 'paid',
            'student_name' => $order->student?->user?->name ?? 'Siswa',
        ]);
    }

    /**
     * Show Canteen Vendor Finance / Balance page.
     */
    public function finance()
    {
        $stall = $this->getVendorStall();
        $withdrawals = CanteenWithdrawal::where('stall_id', $stall->id)->latest()->get();
        $transactions = CanteenTransaction::where('stall_id', $stall->id)->latest()->get();

        return view('canteen-vendor.finance', compact('stall', 'withdrawals', 'transactions'));
    }

    /**
     * Update Bank Account settings.
     */
    public function updateBankAccount(Request $request)
    {
        $stall = $this->getVendorStall();
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
        ]);

        $stall->update($validated);

        return redirect()->back()->with('success', 'Rekening bank berhasil diperbarui.');
    }

    /**
     * Request a withdrawal from stall balance.
     */
    public function requestWithdrawal(Request $request)
    {
        $stall = $this->getVendorStall();
        $minWithdrawal = (int)\App\Models\Setting::get('canteen_min_withdrawal', '10000');
        
        $request->validate([
            'amount' => 'required|numeric|min:' . $minWithdrawal,
        ], [
            'amount.required' => 'Nominal penarikan wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.min' => 'Nominal penarikan minimal Rp ' . number_format($minWithdrawal, 0, ',', '.') . '.',
        ]);

        if ($stall->balance < $request->amount) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk melakukan penarikan.');
        }

        if (empty($stall->bank_name) || empty($stall->bank_account_number) || empty($stall->bank_account_name)) {
            return redirect()->back()->with('error', 'Silakan lengkapi informasi rekening bank terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            // Deduct from balance (hold it in withdrawal request)
            $stall->decrement('balance', $request->amount);

            CanteenWithdrawal::create([
                'stall_id' => $stall->id,
                'amount' => $request->amount,
                'bank_name' => $stall->bank_name,
                'bank_account_number' => $stall->bank_account_number,
                'bank_account_name' => $stall->bank_account_name,
                'status' => 'pending',
            ]);

            CanteenTransaction::create([
                'stall_id' => $stall->id,
                'type' => 'withdraw',
                'amount' => $request->amount,
                'balance_after' => $stall->balance,
                'reference_no' => 'WD-' . time(),
                'description' => 'Penarikan saldo (Menunggu Persetujuan Admin)',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Permintaan penarikan saldo berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
