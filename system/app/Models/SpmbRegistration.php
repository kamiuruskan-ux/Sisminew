<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'wave_id',
        'registration_number',
        'nisn',
        'nik',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'email',
        'parent_name',
        'parent_phone',
        'parent_address',
        'origin_school',
        'custom_fields',
        'class_id',
        'major_id',
        'status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'payment_proof',
        'payment_status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
        'registration_fee' => 'integer',
        'custom_fields' => 'array',
    ];

    public function getCustomFieldValue(string $key, $default = '-')
    {
        if (!is_array($this->custom_fields) || !isset($this->custom_fields[$key])) {
            return $default;
        }
        $val = $this->custom_fields[$key];
        if (is_array($val)) {
            return implode(', ', $val);
        }
        return !empty($val) ? $val : $default;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wave(): BelongsTo
    {
        return $this->belongsTo(Wave::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SpmbDocument::class);
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return get_public_file_url($this->payment_proof, 'img/spmb/proofs');
    }

    public function getWhatsappPhoneAttribute(): ?string
    {
        $phone = $this->parent_phone ?: $this->phone;
        if (!$phone) {
            return null;
        }
        $formatted = \App\Services\WhatsAppService::formatPhoneNumber($phone);
        return !empty($formatted) ? $formatted : null;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        $waPhone = $this->whatsapp_phone;
        if (!$waPhone) {
            return null;
        }

        $schoolName = Setting::get('school_name', 'Sekolah');
        if ($this->payment_status === 'paid' && $this->status === 'draft') {
            $message = "Halo Bapak/Ibu orang tua dari *{$this->full_name}*,\n\nKami dari panitia SPMB {$schoolName} menginformasikan bahwa pembayaran uang pendaftaran ananda (No. Registrasi: *{$this->registration_number}*) telah berhasil terkonfirmasi lunas.\n\nNamun, formulir pendaftaran siswa tercatat masih berstatus *Draft (belum selesai diisi)*.\n\nMohon untuk segera login ke portal SPMB guna melengkapi formulir data diri dan mengunggah dokumen persyaratan di tautan berikut:\n" . route('spmb.dashboard.index') . "\n\nTerima kasih atas kerja samanya.";
        } else {
            $message = "Halo Bapak/Ibu orang tua dari *{$this->full_name}*,\n\nKami dari panitia SPMB {$schoolName} menghubungi terkait pendaftaran calon siswa baru ananda dengan No. Registrasi *{$this->registration_number}*.\n\nPortal SPMB: " . route('spmb.dashboard.index') . "\n\nTerima kasih.";
        }

        return "https://wa.me/{$waPhone}?text=" . urlencode($message);
    }
}
