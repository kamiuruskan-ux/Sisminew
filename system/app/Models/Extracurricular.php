<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Extracurricular extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'schedule',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected $appends = [
        'category_label',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return get_public_file_url($this->image, 'img/extracurriculars');
    }

    public function categoryModel()
    {
        return $this->belongsTo(ExtracurricularCategory::class, 'category', 'slug');
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->getCategoryNameAttribute();
    }

    public function getCategoryNameAttribute(): string
    {
        if ($this->relationLoaded('categoryModel') && $this->categoryModel) {
            return $this->categoryModel->name;
        }

        $name = ExtracurricularCategory::getCategoryNameBySlug($this->category);
        if ($name) {
            return $name;
        }

        return !empty($this->category) ? ucfirst($this->category) : 'Wajib';
    }

    public function members()
    {
        return $this->hasMany(ExtracurricularMember::class, 'extracurricular_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'extracurricular_members', 'extracurricular_id', 'student_id')->withTimestamps();
    }
}
