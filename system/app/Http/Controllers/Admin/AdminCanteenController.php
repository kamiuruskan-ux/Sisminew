<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanteenCategory;
use App\Models\CanteenItem;
use App\Models\CanteenOrder;
use App\Models\CanteenOrderItem;
use App\Models\CanteenStall;
use App\Models\CanteenWithdrawal;
use App\Models\CanteenTransaction;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCanteenController extends Controller
{


    /**
     * Live Orders Management Board.
     */
    public function orders(Request $request)
    {
        $query = CanteenOrder::with(['student.user', 'items', 'cashier']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('qr_code', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15);

        $counts = [
            'all' => CanteenOrder::count(),
            'pending' => CanteenOrder::where('order_status', 'pending')->count(),
            'processing' => CanteenOrder::where('order_status', 'processing')->count(),
            'ready' => CanteenOrder::where('order_status', 'ready')->count(),
            'completed' => CanteenOrder::where('order_status', 'completed')->count(),
        ];

        return view('admin.canteen.orders', compact('orders', 'counts'));
    }

    /**
     * Update order & payment status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'nullable|in:pending,processing,ready,completed,cancelled',
            'payment_status' => 'nullable|in:unpaid,paid,refunded',
        ]);

        $order = CanteenOrder::findOrFail($id);
        $updateData = [];

        if ($request->filled('order_status')) {
            $updateData['order_status'] = $request->order_status;
        }

        if ($request->filled('payment_status')) {
            $updateData['payment_status'] = $request->payment_status;
            if ($request->payment_status === 'paid' && !$order->paid_at) {
                $updateData['paid_at'] = now();
                $updateData['cashier_id'] = auth()->id();
            }
        }

        $order->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui.',
                'order' => $order,
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Items & Stock Management View.
     */
    public function items(Request $request)
    {
        $categories = CanteenCategory::all();
        if ($categories->isEmpty()) {
            CanteenCategory::create(['name' => 'Makanan Utama', 'slug' => 'makanan-utama', 'icon' => 'Utensils']);
            CanteenCategory::create(['name' => 'Minuman', 'slug' => 'minuman', 'icon' => 'CupSoda']);
            $categories = CanteenCategory::all();
        }

        $stalls = CanteenStall::all();
        if ($stalls->isEmpty()) {
            CanteenStall::create(['name' => 'Kantin Utama', 'slug' => 'kantin-utama', 'owner_name' => 'Pengelola Kantin', 'phone' => '-']);
            $stalls = CanteenStall::all();
        }

        $query = CanteenItem::with(['category', 'stall']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stall_id')) {
            $query->where('stall_id', $request->stall_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $items = $query->latest()->paginate(12);

        return view('admin.canteen.items', compact('items', 'categories', 'stalls'));
    }

    /**
     * Store new Canteen Item.
     */
    public function storeItem(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:canteen_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stall_id' => 'nullable|exists:canteen_stalls,id',
            'stall_name' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,jfif,svg|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $savedImage = save_uploaded_public_file($request->file('image'), 'img/canteen/items');
            $imagePath = 'canteen/items/' . basename($savedImage);
        }

        $stallId = $request->stall_id;
        $stallName = $request->stall_name;

        if ($stallId) {
            $stall = CanteenStall::find($stallId);
            $stallName = $stall ? $stall->name : ($stallName ?? 'Kantin Utama');
        } elseif (!empty($stallName)) {
            $stall = CanteenStall::firstOrCreate(
                ['name' => $stallName],
                ['owner_name' => 'Pengelola Kantin', 'phone' => '-']
            );
            $stallId = $stall->id;
        } else {
            $stall = CanteenStall::firstOrCreate(
                ['name' => 'Kantin Utama'],
                ['owner_name' => 'Pengelola Kantin', 'phone' => '-']
            );
            $stallId = $stall->id;
            $stallName = $stall->name;
        }

        CanteenItem::create([
            'category_id' => $request->category_id,
            'stall_id' => $stallId,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'stall_name' => $stallName,
            'image' => $imagePath,
            'is_available' => true,
        ]);

        return back()->with('success', 'Produk kantin berhasil ditambahkan.');
    }

    /**
     * Update Canteen Item.
     */
    public function updateItem(Request $request, $id)
    {
        $item = CanteenItem::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:canteen_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stall_id' => 'nullable|exists:canteen_stalls,id',
            'stall_name' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,jfif,svg|max:5120',
        ]);

        $imagePath = $item->image;
        if ($request->hasFile('image')) {
            if ($item->image) {
                delete_public_file('img/' . ltrim($item->image, '/'));
            }
            $savedImage = save_uploaded_public_file($request->file('image'), 'img/canteen/items');
            $imagePath = 'canteen/items/' . basename($savedImage);
        }

        $stallId = $request->stall_id ?? $item->stall_id;
        $stallName = $request->stall_name ?? $item->stall_name;

        if ($request->filled('stall_id')) {
            $stall = CanteenStall::find($request->stall_id);
            if ($stall) {
                $stallName = $stall->name;
            }
        } elseif (!empty($request->stall_name)) {
            $stall = CanteenStall::firstOrCreate(
                ['name' => $request->stall_name],
                ['owner_name' => 'Pengelola Kantin', 'phone' => '-']
            );
            $stallId = $stall->id;
            $stallName = $stall->name;
        }

        $item->update([
            'category_id' => $request->category_id,
            'stall_id' => $stallId,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'stall_name' => $stallName,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? true : $item->is_available,
        ]);

        return back()->with('success', 'Produk kantin berhasil diperbarui.');
    }

    /**
     * Delete Canteen Item.
     */
    public function destroyItem($id)
    {
        $item = CanteenItem::findOrFail($id);
        if ($item->image) {
            if (\Illuminate\Support\Facades\File::exists(public_path('img/' . $item->image))) {
                \Illuminate\Support\Facades\File::delete(public_path('img/' . $item->image));
            }
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        return back()->with('success', 'Produk kantin berhasil dihapus.');
    }

    /**
     * Toggle item availability.
     */
    public function toggleItem($id)
    {
        $item = CanteenItem::findOrFail($id);
        $item->update(['is_available' => !$item->is_available]);

        return back()->with('success', 'Status ketersediaan produk diperbarui.');
    }

    /**
     * Canteen Sales Reports.
     */
    public function reports(Request $request)
    {
        $today = now()->format('Y-m-d');
        
        $todayRevenue = CanteenOrder::where('payment_status', 'paid')
            ->whereDate('paid_at', $today)
            ->sum('total_amount');

        $todayOrders = CanteenOrder::whereDate('created_at', $today)->count();

        $monthRevenue = CanteenOrder::where('payment_status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        $recentPaidOrders = CanteenOrder::where('payment_status', 'paid')
            ->with(['student.user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        $topItems = CanteenOrderItem::select('item_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('admin.canteen.reports', compact('todayRevenue', 'todayOrders', 'monthRevenue', 'recentPaidOrders', 'topItems'));
    }

    /**
     * Print Canteen Sales Reports.
     */
    public function printReports(Request $request)
    {
        $today = now()->format('Y-m-d');
        
        $todayRevenue = CanteenOrder::where('payment_status', 'paid')
            ->whereDate('paid_at', $today)
            ->sum('total_amount');

        $todayOrders = CanteenOrder::whereDate('created_at', $today)->count();

        $monthRevenue = CanteenOrder::where('payment_status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        $recentPaidOrders = CanteenOrder::where('payment_status', 'paid')
            ->with(['student.user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        $topItems = CanteenOrderItem::select('item_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('admin.canteen.print-report', compact('todayRevenue', 'todayOrders', 'monthRevenue', 'recentPaidOrders', 'topItems'));
    }

    /**
     * Scan student QR code payload for POS checkout.
     */
    public function scanStudent(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCode = trim($request->qr_code);
        $student = null;

        if (Str::startsWith($qrCode, '{')) {
            $decoded = json_decode($qrCode, true);
            if (isset($decoded['qr_code'])) {
                $qrCode = $decoded['qr_code'];
            }
        }

        $student = Student::where('qr_code', $qrCode)
            ->orWhere('nisn', $qrCode)
            ->with('user', 'class')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan untuk QR Code/NISN tersebut.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->user?->name,
                'nisn' => $student->nisn,
                'class' => $student->class?->name ?? '-',
                'savings_balance' => $student->savings_balance,
                'balance_formatted' => 'Rp ' . number_format($student->savings_balance, 0, ',', '.'),
            ]
        ]);
    }

    /**
     * List all canteen withdrawal requests.
     */
    public function withdrawals(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = CanteenWithdrawal::with(['stall']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $withdrawals = $query->latest()->paginate(15)->withQueryString();

        return view('admin.canteen.withdrawals', compact('withdrawals', 'status'));
    }

    /**
     * Approve canteen withdrawal request.
     */
    public function approveWithdrawal($id)
    {
        $withdrawal = CanteenWithdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses.');
        }

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Update transaction to approved description
            CanteenTransaction::where('stall_id', $withdrawal->stall_id)
                ->where('type', 'withdraw')
                ->where('amount', $withdrawal->amount)
                ->where('description', 'like', '%Menunggu Persetujuan Admin%')
                ->latest()
                ->first()
                ?->update([
                    'description' => 'Penarikan saldo disetujui oleh Admin',
                ]);

            DB::commit();
            return redirect()->back()->with('success', 'Permintaan penarikan saldo berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject canteen withdrawal request.
     */
    public function rejectWithdrawal(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $withdrawal = CanteenWithdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses.');
        }

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Return balance back to the canteen stall
            $stall = CanteenStall::findOrFail($withdrawal->stall_id);
            $stall->increment('balance', $withdrawal->amount);

            // Log refund transaction
            CanteenTransaction::create([
                'stall_id' => $stall->id,
                'type' => 'refund',
                'amount' => $withdrawal->amount,
                'balance_after' => $stall->balance,
                'reference_no' => 'REJ-' . time(),
                'description' => 'Pengembalian saldo: Penarikan ditolak oleh Admin. Alasan: ' . $request->rejection_reason,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Permintaan penarikan saldo ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * List & Manage Canteen Vendor Users.
     */
    public function users(Request $request)
    {
        $query = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['kantin', 'canteen']);
        })->with(['roles']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $canteenUsers = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::whereHas('roles', fn($q) => $q->whereIn('slug', ['kantin', 'canteen']))->count(),
            'active' => User::whereHas('roles', fn($q) => $q->whereIn('slug', ['kantin', 'canteen']))->where('status', 'active')->count(),
        ];

        return view('admin.canteen.users', compact('canteenUsers', 'stats'));
    }

    /**
     * Store new Canteen Vendor User Account.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama pengelola/kantin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar pada pengguna lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $savedAvatar = save_uploaded_public_file($request->file('avatar'), 'img/avatars');
            $avatarPath = 'avatars/' . basename($savedAvatar);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'avatar' => $avatarPath,
            'status' => 'active',
        ]);

        $canteenRole = Role::whereIn('slug', ['kantin', 'canteen'])->first();
        if (!$canteenRole) {
            $canteenRole = Role::create([
                'name' => 'Kantin / Vendor',
                'slug' => 'kantin',
                'description' => 'Role Pengelola Vendor Kantin Sekolah',
                'is_active' => true,
            ]);
        }
        $user->assignRole($canteenRole);

        return redirect()->route('admin.canteen.users')
            ->with('success', 'Akun Pengelola Kantin (' . $user->name . ') berhasil dibuat.');
    }

    /**
     * Update Canteen Vendor User Account.
     */
    public function updateUser(Request $request, $id)
    {
        $userId = is_numeric($id) ? $id : decode_id($id);
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama pengelola/kantin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar pada pengguna lain.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                delete_public_file($user->avatar, 'img/avatars');
            }
            $savedAvatar = save_uploaded_public_file($request->file('avatar'), 'img/avatars');
            $user->avatar = 'avatars/' . basename($savedAvatar);
        }

        $user->save();

        return redirect()->route('admin.canteen.users')
            ->with('success', 'Akun Pengelola Kantin (' . $user->name . ') berhasil diperbarui.');
    }

    /**
     * Delete Canteen Vendor User Account.
     */
    public function destroyUser($id)
    {
        $userId = is_numeric($id) ? $id : decode_id($id);
        $user = User::findOrFail($userId);

        if ($user->avatar) {
            delete_public_file($user->avatar, 'img/avatars');
        }

        $user->delete();

        return redirect()->route('admin.canteen.users')
            ->with('success', 'Akun Kantin berhasil dihapus.');
    }

    /**
     * Toggle Canteen Vendor User Status (Active / Inactive).
     */
    public function toggleUserStatus(Request $request, $id)
    {
        $userId = is_numeric($id) ? $id : decode_id($id);
        $user = User::findOrFail($userId);

        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'Status akun ' . $user->name . ' diubah menjadi ' . ($user->status === 'active' ? 'Aktif' : 'Nonaktif') . '.');
    }
}
