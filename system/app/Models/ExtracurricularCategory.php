<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ExtracurricularCategory extends Model
{
    use HasFactory;

    protected $table = 'extracurricular_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('extracurricular_categories')) {
            Schema::create('extracurricular_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('color', 50)->default('emerald');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed kategori default
            $defaults = [
                ['name' => 'Kepemimpinan & Organisasi', 'slug' => 'kepemimpinan', 'color' => 'indigo', 'order' => 1],
                ['name' => 'Sains & Teknologi', 'slug' => 'sains', 'color' => 'blue', 'order' => 2],
                ['name' => 'Olahraga & Atletik', 'slug' => 'olahraga', 'color' => 'emerald', 'order' => 3],
                ['name' => 'Seni & Budaya', 'slug' => 'seni', 'color' => 'amber', 'order' => 4],
            ];

            foreach ($defaults as $def) {
                self::create($def);
            }
        }
    }

    protected static ?array $cachedCategoryMap = null;

    public static function getCategoryList(): array
    {
        self::ensureTableExists();

        $cats = self::where('is_active', true)->orderBy('order')->orderBy('name')->get();
        if ($cats->isEmpty()) {
            return [
                'kepemimpinan' => 'Kepemimpinan & Organisasi',
                'sains' => 'Sains & Teknologi',
                'olahraga' => 'Olahraga & Atletik',
                'seni' => 'Seni & Budaya',
            ];
        }

        return $cats->pluck('name', 'slug')->toArray();
    }

    public static function getCategoryNameBySlug(?string $slug): ?string
    {
        if (empty($slug)) {
            return null;
        }

        if (static::$cachedCategoryMap === null) {
            self::ensureTableExists();
            static::$cachedCategoryMap = self::pluck('name', 'slug')->toArray();
        }

        if (isset(static::$cachedCategoryMap[$slug])) {
            return static::$cachedCategoryMap[$slug];
        }

        // Cek jika kategori disimpan sebagai ID
        if (is_numeric($slug)) {
            $catById = self::find((int) $slug);
            if ($catById) {
                static::$cachedCategoryMap[$slug] = $catById->name;
                return $catById->name;
            }
        }

        // Cek jika dicari langsung dari DB jika ada record baru atau pencarian berdasarkan nama
        $cat = self::where('slug', $slug)->orWhere('name', $slug)->first();
        if ($cat) {
            static::$cachedCategoryMap[$slug] = $cat->name;
            return $cat->name;
        }

        return null;
    }

    public static function clearCache(): void
    {
        static::$cachedCategoryMap = null;
    }
}
