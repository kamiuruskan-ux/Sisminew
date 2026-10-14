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
}
