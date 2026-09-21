<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeMutabaah;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class EmployeeMutabaahController extends Controller
{
    /**
     * Input & History Mutabaah Ibadah Harian Pegawai (Self-Service)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->input('date', date('Y-m-d'));
        $targetDate = Carbon::parse($date);

        // Find or instantiate today's record
        $mutabaah = EmployeeMutabaah::firstOrNew([
            'user_id' => $user->id,
            'date' => $targetDate->format('Y-m-d'),
        ]);

        // Monthly stats for current user
        $currentMonthStart = $targetDate->copy()->startOfMonth();
        $currentMonthEnd = $targetDate->copy()->endOfMonth();

        $monthlyEntries = EmployeeMutabaah::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonthStart->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get();

        $daysFilled = $monthlyEntries->count();
        $avgScore = $daysFilled > 0 ? round($monthlyEntries->avg('daily_score'), 1) : 0;

        // Recent 7 days for quick overview
        $recentDays = EmployeeMutabaah::where('user_id', $user->id)
            ->where('date', '<=', now()->format('Y-m-d'))
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        return view('admin.employee-mutabaah.index', compact(
            'user',
            'date',
            'targetDate',
            'mutabaah',
            'monthlyEntries',
            'daysFilled',
            'avgScore',
            'recentDays'
        ));
    }

    /**
     * Store or update employee mutabaah record
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'date' => 'required|date',
            'rawatib_count' => 'nullable|integer|min:0|max:20',
            'tilawah_pages' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $date = $request->input('date');

        $mutabaah = EmployeeMutabaah::firstOrNew([
            'user_id' => $user->id,
            'date' => $date,
        ]);

        $mutabaah->subuh_jamaah = $request->boolean('subuh_jamaah');
        $mutabaah->dzuhur_jamaah = $request->boolean('dzuhur_jamaah');
        $mutabaah->ashar_jamaah = $request->boolean('ashar_jamaah');
        $mutabaah->maghrib_jamaah = $request->boolean('maghrib_jamaah');
        $mutabaah->isya_jamaah = $request->boolean('isya_jamaah');

        $mutabaah->rawatib_count = (int) $request->input('rawatib_count', 0);
        $mutabaah->dhuha = $request->boolean('dhuha');
        $mutabaah->tahajjud_witir = $request->boolean('tahajjud_witir');

        $mutabaah->tilawah_pages = (int) $request->input('tilawah_pages', 0);
        $mutabaah->dzikir_pagi_petang = $request->boolean('dzikir_pagi_petang');

        $mutabaah->puasa_sunnah = $request->boolean('puasa_sunnah');
        $mutabaah->sedekah = $request->boolean('sedekah');
        $mutabaah->notes = $request->input('notes');

        $mutabaah->save();

        return redirect()->route('admin.employee-mutabaah.index', ['date' => $date])
            ->with('success', "Mutabaah amalan harian tanggal " . Carbon::parse($date)->translatedFormat('d F Y') . " berhasil disimpan! Skor harian: {$mutabaah->daily_score} poin.");
    }

    /**
     * Rekap Mutabaah Seluruh Pegawai (Khusus Kepala Sekolah / Admin / Supervisor)
     */
    public function recap(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $search = trim($request->input('search', ''));

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $employeeRoles = ['guru', 'teacher', 'guru-quran', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'wakasek-kesiswaan', 'wakasek-kurikulum', 'wakasek-kehumasan', 'bendahara', 'guru-bk'];
        
        $query = User::where(function ($q) use ($employeeRoles) {
            $q->whereHas('roles', function ($rq) use ($employeeRoles) {
                $rq->whereIn('slug', $employeeRoles);
            })->orWhere(function ($rq) {
                $rq->whereNotNull('jabatan')->where('jabatan', '!=', '');
            });
        });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->get();

        $recapData = [];
        $totalDaysInMonth = $startDate->daysInMonth;

        foreach ($employees as $emp) {
            $records = EmployeeMutabaah::where('user_id', $emp->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            $filledCount = $records->count();
            $averageScore = $filledCount > 0 ? round($records->avg('daily_score'), 1) : 0;
            $sholatSubuhPercent = $filledCount > 0 ? round(($records->where('subuh_jamaah', true)->count() / $filledCount) * 100) : 0;
            $tahajjudPercent = $filledCount > 0 ? round(($records->where('tahajjud_witir', true)->count() / $filledCount) * 100) : 0;
            $tilawahAvgPages = $filledCount > 0 ? round($records->avg('tilawah_pages'), 1) : 0;

            $recapData[] = [
                'user' => $emp,
                'days_filled' => $filledCount,
                'compliance_percent' => round(($filledCount / $totalDaysInMonth) * 100, 1),
                'average_score' => $averageScore,
                'subuh_percent' => $sholatSubuhPercent,
                'tahajjud_percent' => $tahajjudPercent,
                'tilawah_avg' => $tilawahAvgPages,
            ];
        }

        return view('admin.employee-mutabaah.recap', compact('recapData', 'year', 'month', 'search', 'totalDaysInMonth'));
    }
}
