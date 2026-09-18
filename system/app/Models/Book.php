<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'category',
        'isbn',
        'description',
        'cover_path',
        'file_path',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getCoverUrlAttribute(): ?string
    {
        return get_public_file_url($this->cover_path, 'img/books/covers');
    }

    public function getFileUrlAttribute(): ?string
    {
        return get_public_file_url($this->file_path, 'doc/books/pdf');
    }
}
