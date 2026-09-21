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
        $avgScore = $daysFilled > 0 ? round($monthlyEntries->avg('daily_score'), 1) : 95.0;

        // Generate 7 days ending at $targetDate (matching Image 5 Riwayat 7 Hari)
        $historyDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $targetDate->copy()->subDays($i);
            $dateStr = $d->format('Y-m-d');
            $hasRecord = EmployeeMutabaah::where('user_id', $user->id)
                ->where('date', $dateStr)
                ->exists();
            
            $dayAbbr = match($d->dayOfWeek) {
                0 => 'Min',
                1 => 'Sen',
                2 => 'Sel',
                3 => 'Rab',
                4 => 'Kam',
                5 => 'Jum',
                6 => 'Sab',
            };
            $label = $dayAbbr . ', ' . $d->format('j/n');
            
            $historyDays[] = [
                'date' => $dateStr,
                'label' => $label,
                'has_record' => $hasRecord,
                'is_current' => ($dateStr === $targetDate->format('Y-m-d')),
            ];
        }

        // Check weekly tahajud status
        $weekStart = $targetDate->copy()->startOfWeek();
        $weekEnd = $targetDate->copy()->endOfWeek();
        $tahajudThisWeek = EmployeeMutabaah::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->where(function($q) {
                $q->where('sholat_tahajud', true)->orWhere('tahajjud_witir', true);
            })
            ->exists();

        // Check monthly puasa status
        $puasaThisMonth = EmployeeMutabaah::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonthStart->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
            ->where('puasa_sunnah', true)
            ->exists();

        return view('admin.employee-mutabaah.index', compact(
            'user',
            'date',
            'targetDate',
            'mutabaah',
            'monthlyEntries',
            'daysFilled',
            'avgScore',
            'historyDays',
            'tahajudThisWeek',
            'puasaThisMonth'
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
            'notes' => 'nullable|string|max:500',
        ]);

        $date = $request->input('date');

        $mutabaah = EmployeeMutabaah::firstOrNew([
            'user_id' => $user->id,
            'date' => $date,
        ]);

        // 6 Checklist Amalan from Image 5
        $mutabaah->sholat_fardhu = $request->boolean('sholat_fardhu');
        $mutabaah->rawatib_dhuha = $request->boolean('rawatib_dhuha');
        $mutabaah->tilawah_quran = $request->boolean('tilawah_quran');
        $mutabaah->dzikir_pagi_petang = $request->boolean('dzikir_pagi_petang');
        $mutabaah->sholat_tahajud = $request->boolean('sholat_tahajud');
        $mutabaah->puasa_sunnah = $request->boolean('puasa_sunnah');

        // Sync legacy columns for backward compatibility
        if ($mutabaah->sholat_fardhu) {
            $mutabaah->subuh_jamaah = true;
            $mutabaah->dzuhur_jamaah = true;
            $mutabaah->ashar_jamaah = true;
            $mutabaah->maghrib_jamaah = true;
            $mutabaah->isya_jamaah = true;
        } else {
            $mutabaah->subuh_jamaah = false;
            $mutabaah->dzuhur_jamaah = false;
            $mutabaah->ashar_jamaah = false;
            $mutabaah->maghrib_jamaah = false;
            $mutabaah->isya_jamaah = false;
        }

        if ($mutabaah->rawatib_dhuha) {
            $mutabaah->rawatib_count = 10;
            $mutabaah->dhuha = true;
        } else {
            $mutabaah->rawatib_count = 0;
            $mutabaah->dhuha = false;
        }

        if ($mutabaah->tilawah_quran) {
            $mutabaah->tilawah_pages = 10;
        } else {
            $mutabaah->tilawah_pages = 0;
        }

        if ($mutabaah->sholat_tahajud) {
            $mutabaah->tahajjud_witir = true;
        } else {
            $mutabaah->tahajjud_witir = false;
        }

        $mutabaah->notes = $request->input('notes');
        $mutabaah->save();

        return redirect()->route('admin.employee-mutabaah.index', ['date' => $date])
            ->with('success', "Mutabaah amalan guru tanggal " . Carbon::parse($date)->translatedFormat('d F Y') . " berhasil disimpan! Skor harian: {$mutabaah->daily_score} poin.");
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
