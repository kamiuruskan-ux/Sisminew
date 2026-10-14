<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BpiAtp extends Model
{
    use HasFactory;

    protected $table = 'bpi_atps';

    protected $fillable = [
        'title',
        'dimension',
        'phase',
        'level',
        'class_indicator',
        'learning_outcome',
        'assessment_indicators',
        'habits_required',
        'execution_time',
        'meeting_activities',
        'material_content',
        'order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'meeting_activities' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('bpi_atps')) {
            Schema::create('bpi_atps', function (Blueprint $table) {
                $table->id();
                $table->string('title'); // Judul Materi
                $table->string('dimension', 255)->nullable(); // Dimensi
                $table->string('phase', 100)->nullable(); // Capaian Fase
                $table->string('level', 100)->nullable(); // Tingkat / Kelas
                $table->text('class_indicator')->nullable(); // Indikator Capaian Kelas
                $table->text('learning_outcome')->nullable(); // Capaian Pembelajaran
                $table->text('assessment_indicators')->nullable(); // Indikator yang dinilai
                $table->text('habits_required')->nullable(); // Pembiasaan yang perlu
                $table->string('execution_time', 150)->nullable(); // Waktu Pelaksanaan
                $table->json('meeting_activities')->nullable(); // Aktivitas per pertemuan 1, 2, 3, dst
                $table->longText('material_content')->nullable(); // Teks materi pembelajaran untuk guru
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['level', 'is_active']);
            });

            // Seed contoh modul ATP awal
            self::create([
                'title' => 'Adab Mulia: Berbakti kepada Kedua Orang Tua & Guru',
                'dimension' => 'Beriman, Bertakwa kepada Tuhan YME, dan Berakhlak Mulia',
                'phase' => 'Fase A & B (Tingkat 1 - 4)',
                'level' => 'Semua Tingkat',
                'learning_outcome' => 'Santri memahami kedudukan berbakti (birrul walidain) dan penghormatan kepada guru sebagai pondasi keberkahan ilmu dan kehidupan.',
                'class_indicator' => 'Santri mampu mengucapkan salam, mendoakan orang tua setiap setelah sholat, dan mendengarkan penjelasan guru dengan tawadhu.',
                'assessment_indicators' => 'Santri terbiasa mencium tangan orang tua/guru, bertutur kata santun tanpa membentak, serta mencatat nasihat kebaikan.',
                'habits_required' => 'Mendoakan orang tua setiap selesai sholat 5 waktu, membantu pekerjaan rumah tangga ringan, serta tertib di majelis halaqah.',
                'execution_time' => 'Semester Ganjil - Pekan ke-1 s/d 4',
                'meeting_activities' => [
                    ['meeting' => 1, 'activity' => 'Tadabbur QS. Al-Isra ayat 23-24 tentang perintah tidak berucap kasar (ah/cih) kepada orang tua.'],
                    ['meeting' => 2, 'activity' => 'Kisah teladan Uwais Al-Qarni dan kemuliaan baktinya menggendong ibunda ke tanah suci.'],
                    ['meeting' => 3, 'activity' => 'Praktek adab berbicara sopan, menatap wajah dengan kasih sayang, dan meminta ridho sebelum beraktivitas.'],
                    ['meeting' => 4, 'activity' => 'Evaluasi mutaba\'ah yaumiyah adab di rumah dan komitmen surat cinta untuk ayah bunda.']
                ],
                'material_content' => "## Panduan Materi Murabbi: Birrul Walidain & Menghormati Guru\n\n### 1. Muqaddimah & Landasan Syar'i\nAllah SWT menyandingkan perintah tauhid kepada-Nya dengan perintah berbuat baik kepada kedua orang tua:\n> *\"Dan Tuhanmu telah memerintahkan supaya kamu jangan menyembah selain Dia dan hendaklah kamu berbuat baik pada ibu bapakmu dengan sebaik-baiknya...\"* (QS. Al-Isra: 23)\n\n### 2. Poin Inti Penyampaian ke Santri\n- **Ibu mengandung dalam susah payah:** Ingatkan pengorbanan bunda selama 9 bulan dan merawat hingga kini.\n- **Ayah mencari nafkah halal:** Mengingatkan santri jerih payah ayah mencari rizki barokah untuk pendidikan ananda.\n- **Guru adalah pewaris nabi:** Mengajarkan ilmu akhirat dan dunia yang menyelamatkan dari kebodohan.\n\n### 3. Langkah Interaktif Pembinaan\n1. Ajak santri menyebutkan 3 hal kebaikan orang tua yang paling mereka syukuri hari ini.\n2. Latihan membiasakan kata-kata ajaib: *Terima kasih*, *Maaf*, dan *Tolong*.\n3. Pandu santri menulis doa rabba-ghfirli waliwaalidayya warhamhuma kamaa rabbayaani shaghiira.",
                'order' => 1,
                'is_active' => true,
            ]);

            self::create([
                'title' => 'Istiqomah Sholat Fardhu Tepat Waktu & Dhuha',
                'dimension' => 'Dimensi Keimanan & Ketaqwaan Pribadi Muslim',
                'phase' => 'Fase B & C (Tingkat 3 - 6)',
                'level' => 'Tingkat 3-6',
                'learning_outcome' => 'Santri menjadikan sholat sebagai kebutuhan ruhani dan tiang agama yang dijaga dengan kesadaran sendiri.',
                'class_indicator' => 'Santri mampu wudhu secara sempurna, bergegas ke masjid saat adzan, serta melaksanakan sholat berjamaah.',
                'assessment_indicators' => 'Mutaba\'ah kartu sholat fardhu 5 waktu tanpa bolong dan rutin sholat dhuha 2 atau 4 rakaat.',
                'habits_required' => 'Merapikan shaf sholat, dzikir sesudah sholat minimal tasbih, tahmid, takbir 33 kali, serta tilawah satu lembar.',
                'execution_time' => 'Semester Ganjil - Pekan ke-5 s/d 8',
                'meeting_activities' => [
                    ['meeting' => 1, 'activity' => 'Keagungan Sholat sebagai amalan pertama yang dihisab pada hari kiamat.'],
                    ['meeting' => 2, 'activity' => 'Praktek wudhu sempurna (isbaghul wudhu) sesuai Sunnah Rasulullah SAW.'],
                    ['meeting' => 3, 'activity' => 'Mengenal keutamaan Sholat Berjamaah 27 derajat dan sholat sunnah Dhuha sebagai sedekah persendian.'],
                    ['meeting' => 4, 'activity' => 'Bedah mutaba\'ah sholat harian kelompok halaqah dan pemberian apresiasi bintang istiqomah.']
                ],
                'material_content' => "## Panduan Materi Murabbi: Sholat Tiang Agama\n\n### Dalil Kunci:\n> *\"Sesungguhnya shalat itu mencegah dari (perbuatan-perbuatan) keji dan mungkar.\"* (QS. Al-Ankabut: 45)\n\n### Tips Pendekatan Guru BPI:\n- Jangan hanya menghakimi santri yang terlambat, tetapi cari tahu kendalanya (apakah begadang, gadget, atau lupa).\n- Tanamkan kecintaan sholat melalui teladan para sahabat nabi yang rindu panggilan adzan.",
                'order' => 2,
                'is_active' => true,
            ]);
        }
    }
}
