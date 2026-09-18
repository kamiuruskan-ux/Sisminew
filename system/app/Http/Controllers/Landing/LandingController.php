<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Major;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function home()
    {
        $sliders = Slider::active()->get();
        $posts = Post::published()->with(['category', 'author'])->latest()->take(6)->get();
        $galleries = Gallery::active()->latest()->take(8)->get();
        $majors = Major::where('is_active', true)->get();
        $announcements = Announcement::published()->latest()->take(4)->get();

        $foundedYear = (int) \App\Support\Setting::get('school_founded_year', 2008);
        $currentYear = (int) date('Y');
        $yearsCount = max(1, $currentYear - $foundedYear);

        $stats = [
            'students' => Student::count(),
            'teachers' => User::role('guru')->count(),
            'achievements' => rtrim(\App\Support\Setting::get('stats_achievements', '150'), '+'),
            'years' => rtrim(\App\Support\Setting::get('stats_years', (string)$yearsCount), '+'),
            'alumni' => rtrim(\App\Support\Setting::get('stats_alumni', '2.500'), '+'),
        ];

        return view('landing.home', compact('sliders', 'posts', 'galleries', 'majors', 'stats', 'announcements'));
    }

    public function spmbInfo()
    {
        // Get all waves (active, upcoming, and inactive) with registration count
        $waves = \App\Models\Wave::withCount('spmbRegistrations')->orderBy('start_date', 'asc')->get();
        
        $totalRegistered = \App\Models\SpmbRegistration::count();
        $totalQuota = \App\Models\Wave::sum('quota');

        return view('landing.spmb-info', compact('waves', 'totalRegistered', 'totalQuota'));
    }

    public function about()
    {
        $foundedYear = (int) \App\Support\Setting::get('school_founded_year', 2008);
        $currentYear = (int) date('Y');
        $yearsCount = max(1, $currentYear - $foundedYear);

        $stats = [
            'students' => Student::count(),
            'teachers' => User::role('guru')->count(),
            'achievements' => rtrim(\App\Support\Setting::get('stats_achievements', '150'), '+'),
            'years' => rtrim(\App\Support\Setting::get('stats_years', (string)$yearsCount), '+'),
            'alumni' => rtrim(\App\Support\Setting::get('stats_alumni', '2.500'), '+'),
        ];

        return view('landing.about', compact('stats'));
    }

    public function blog(Request $request)
    {
        $query = Post::published()->with(['category', 'author', 'tags']);

        // Search by title or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $posts = $query->latest()->paginate(9)->withQueryString();

        return view('landing.blog', compact('posts'));
    }

    public function blogShow($slug)
    {
        $post = Post::with(['category', 'author', 'tags'])->where('slug', $slug)->firstOrFail();
        $post->increment('views');
        
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();

        return view('landing.blog-show', compact('post', 'relatedPosts'));
    }

    public function gallery(Request $request)
    {
        $query = Gallery::active()->with(['category']);

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $galleries = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::where('type', 'gallery')->get();

        return view('landing.gallery', compact('galleries', 'categories'));
    }

    public function curriculum()
    {
        $features = \App\Models\CurriculumFeature::active()->orderBy('order')->get();
        $title = \App\Support\Setting::get('curriculum_title', 'Kurikulum & Program Pendidikan');
        $description = \App\Support\Setting::get('curriculum_description', 'Mengintegrasikan Kurikulum Merdeka Belajar dengan penguatan karakter Pancasila dan literasi teknologi global.');
        return view('landing.curriculum', compact('features', 'title', 'description'));
    }

    public function majors()
    {
        $majors = Major::where('is_active', true)->orderBy('order')->get();
        return view('landing.majors', compact('majors'));
    }

    public function extracurricular()
    {
        $extracurriculars = \App\Models\Extracurricular::active()->orderBy('order')->get();
        return view('landing.extracurricular', compact('extracurriculars'));
    }


    public function events(Request $request)
    {
        $query = Announcement::published();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $announcements = $query->latest()->paginate(9)->withQueryString();

        return view('landing.events', compact('announcements'));
    }

    public function eventsShow($id)
    {
        $event = Announcement::published()->with('author')->find($id);

        if (!$event) {
            $mockups = [
                1 => [
                    'id' => 1,
                    'title' => 'Upacara Peringatan & Gelar Seni Budaya 2024',
                    'type' => 'Agenda Sekolah',
                    'published_at' => \Carbon\Carbon::parse('2024-05-17 07:00:00'),
                    'content' => '<p>Seluruh siswa-siswi dan dewan guru SMA Nusantara diharapkan hadir tepat waktu dengan seragam lengkap untuk mengikuti upacara peringatan Hari Pendidikan serta menyaksikan Gelar Seni Budaya tahunan.</p><h4>Rangkaian Acara:</h4><ul><li>07:00 - 08:00 WIB: Upacara Bendera & Amanat Pembina Upacara</li><li>08:15 - 10:30 WIB: Penampilan Sanggar Seni Tari Tradisional & Musik Nusantara</li><li>10:30 - 12:00 WIB: Pameran Karya Seni Rupa & Produk Kreatif Siswa</li></ul><p><strong>Catatan:</strong> Siswa diwajibkan menjaga ketertiban, kebersihan area lapangan utama, serta mematuhi protokol yang berlaku.</p>',
                    'author' => (object)['name' => 'Panitia Humas & OSIS']
                ],
                2 => [
                    'id' => 2,
                    'title' => 'Jadwal Penilaian Akhir Semester (PAS) Genap',
                    'type' => 'Pengumuman Ujian',
                    'published_at' => \Carbon\Carbon::parse('2024-05-22 08:00:00'),
                    'content' => '<p>Diumumkan kepada seluruh siswa kelas X, XI, dan XII bahwa Penilaian Akhir Semester (PAS) Genap Tahun Ajaran 2023/2024 akan dilaksanakan secara serentak mulai tanggal 22 Mei hingga 05 Juni 2024.</p><h4>Petunjuk Pelaksanaan:</h4><ul><li>Ujian dilaksanakan secara Digital / Computer Based Test (CBT) di Lab Komputer & Ruang Kelas.</li><li>Siswa wajib membawa Kartu Ujian Resmi dan memastikan akun login portal siswa aktif.</li><li>Hadir di lokasi 15 menit sebelum sesi ujian dimulai.</li></ul><p>Semoga seluruh siswa diberikan kelancaran dan hasil yang maksimal.</p>',
                    'author' => (object)['name' => 'Tim Kurikulum & Asesmen']
                ],
                3 => [
                    'id' => 3,
                    'title' => 'Rapat Koordinasi Orang Tua & Komite Sekolah',
                    'type' => 'Pertemuan',
                    'published_at' => \Carbon\Carbon::parse('2024-05-28 09:00:00'),
                    'content' => '<p>Pengurus Komite Sekolah mengundang seluruh Orang Tua / Wali Siswa kelas XII untuk menghadiri Rapat Koordinasi Tahunan dalam rangka evaluasi kelulusan serta sosialisasi program kelanjutan studi perguruan tinggi.</p><h4>Agenda Pembahasan:</h4><ul><li>Laporan Pembelajaran & Evaluasi Hasil Ujian Kelulusan</li><li>Sosialisasi Jalur Masuk Perguruan Tinggi Negeri (SNBP & SNBT)</li><li>Diskusi Program Beasiswa Alumni & Pengembangan Fasilitas Kampus</li></ul><p>Kehadiran Orang Tua / Wali sangat kami harapkan demi kesuksesan putra-putri kita.</p>',
                    'author' => (object)['name' => 'Pengurus Komite Sekolah']
                ]
            ];

            $event = (object) ($mockups[$id] ?? [
                'id' => $id,
                'title' => 'Agenda & Kegiatan Resmi Sekolah #' . $id,
                'type' => 'Kegiatan',
                'published_at' => now(),
                'content' => '<p>Kegiatan resmi ini diselenggarakan dalam rangka meningkatkan kualitas pembelajaran, kedisiplinan, dan prestasi di lingkungan SMA Nusantara.</p><p>Seluruh peserta didik, guru, serta staf pendukung diharapkan mengikuti seluruh rangkaian kegiatan sesuai dengan jadwal dan petunjuk teknis yang berlaku.</p>',
                'author' => (object)['name' => 'Humas Sekolah']
            ]);
        }

        $recentEvents = Announcement::published()
            ->where('id', '!=', $id)
            ->latest()
            ->take(4)
            ->get();

        return view('landing.events-show', compact('event', 'recentEvents'));
    }

    public function contact()
    {
        return view('landing.contact');
    }

    public function privacyPolicy()
    {
        return view('landing.privacy-policy');
    }

    public function termsConditions()
    {
        return view('landing.terms-conditions');
    }
}
