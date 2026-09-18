<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'school_name', 'value' => 'SMA Negeri 1 Contoh', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_short_name', 'value' => 'SMAN 1 Contoh', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_type', 'value' => 'SMA', 'type' => 'select', 'group' => 'general'],
            ['key' => 'npsn', 'value' => '12345678', 'type' => 'text', 'group' => 'general'],
            
            // Contact
            ['key' => 'school_email', 'value' => 'info@sman1contoh.sch.id', 'type' => 'email', 'group' => 'contact'],
            ['key' => 'school_phone', 'value' => '(021) 1234567', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_whatsapp', 'value' => '081234567890', 'type' => 'text', 'group' => 'contact'],
            
            // Address
            ['key' => 'school_address', 'value' => 'Jl. Pendidikan No. 123', 'type' => 'textarea', 'group' => 'address'],
            ['key' => 'school_city', 'value' => 'Jakarta', 'type' => 'text', 'group' => 'address'],
            ['key' => 'school_province', 'value' => 'DKI Jakarta', 'type' => 'text', 'group' => 'address'],
            ['key' => 'school_postal_code', 'value' => '12345', 'type' => 'text', 'group' => 'address'],
            ['key' => 'school_maps_link', 'value' => 'https://maps.google.com/?q=...', 'type' => 'url', 'group' => 'address'],
            
            // Branding
            ['key' => 'primary_color', 'value' => '#3B82F6', 'type' => 'color', 'group' => 'branding'],
            ['key' => 'secondary_color', 'value' => '#1E40AF', 'type' => 'color', 'group' => 'branding'],
            ['key' => 'logo', 'value' => 'img/logo.png', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'logo_path', 'value' => 'img/logo.png', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'school_logo', 'value' => 'img/logo.png', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'favicon', 'value' => 'img/fav.png', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'favicon_path', 'value' => 'img/fav.png', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'school_favicon', 'value' => 'img/fav.png', 'type' => 'image', 'group' => 'branding'],
            
            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/sman1contoh', 'type' => 'url', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/sman1contoh', 'type' => 'url', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/sman1contoh', 'type' => 'url', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/sman1contoh', 'type' => 'url', 'group' => 'social'],
            ['key' => 'tiktok_url', 'value' => 'https://tiktok.com/@sman1contoh', 'type' => 'url', 'group' => 'social'],
            
            // SPMB Settings
            ['key' => 'spmb_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'spmb'],
            ['key' => 'spmb_registration_fee', 'value' => '150000', 'type' => 'number', 'group' => 'spmb'],
            ['key' => 'spmb_info_text', 'value' => 'Pendaftaran Siswa Baru telah dibuka. Segera daftarkan diri Anda!', 'type' => 'textarea', 'group' => 'spmb'],
            
            // Features
            ['key' => 'major_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'blog_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'gallery_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],

            // Dynamic Content Extensions
            ['key' => 'school_tagline', 'value' => 'Berkarakter • Berprestasi • Mendunia', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_video_url', 'value' => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0', 'type' => 'url', 'group' => 'general'],
            ['key' => 'school_founded_year', 'value' => '2008', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_operating_hours', 'value' => 'Senin - Jumat: 07:00 - 16:00 WIB', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_principal_name', 'value' => 'Dr. H. Ahmad Wijaya, M.Pd.', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_principal_nip', 'value' => '19820315 200801 1 003', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_principal_title', 'value' => 'Kepala Sekolah', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_principal_welcome', 'value' => 'Selamat datang di SMA Nusantara. Kami percaya bahwa pendidikan bukan sekadar mentransfer ilmu pengetahuan, melainkan melepaskan potensi terbaik yang ada di dalam diri setiap anak didik.', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'school_principal_photo', 'value' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=500&auto=format&fit=crop', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_website', 'value' => 'www.sekolah.sch.id', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_org_chart_path', 'value' => 'img/org_chart.png', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'stats_achievements', 'value' => '150+', 'type' => 'text', 'group' => 'general'],
            ['key' => 'stats_alumni', 'value' => '2.500+', 'type' => 'text', 'group' => 'general'],

            // Raport Settings Defaults
            ['key' => 'raport_header_title', 'value' => 'RAPORT HASIL BELAJAR SISWA', 'type' => 'text', 'group' => 'raport'],
            ['key' => 'raport_place', 'value' => 'Jakarta', 'type' => 'text', 'group' => 'raport'],
            ['key' => 'raport_date', 'value' => date('d F Y'), 'type' => 'text', 'group' => 'raport'],
            ['key' => 'raport_principal_name', 'value' => 'Dr. H. Ahmad Wijaya, M.Pd.', 'type' => 'text', 'group' => 'raport'],
            ['key' => 'raport_principal_nip', 'value' => '19820315 200801 1 003', 'type' => 'text', 'group' => 'raport'],
            ['key' => 'raport_default_note', 'value' => 'Tingkatkan terus prestasi belajar Anda dan tetap semangat dalam menuntut ilmu.', 'type' => 'textarea', 'group' => 'raport'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
