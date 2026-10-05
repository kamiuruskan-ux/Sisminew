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

    public function isNeedRevision(): bool
    {
        return $this->status === 'need_revision' || ($this->status === 'draft' && !empty($this->verification_notes));
    }

    public function photoDocument()
    {
        return $this->documents->firstWhere('type', 'photo');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'reference_id')->where('reference_type', 'spmb');
    }

    public function getActivePaymentProofAttribute(): ?string
    {
        if (!empty($this->payment_proof)) {
            return $this->payment_proof;
        }

        return PaymentTransaction::where('reference_type', 'spmb')
            ->where('reference_id', $this->id)
            ->whereNotNull('payment_proof')
            ->latest()
            ->value('payment_proof');
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        $proof = $this->active_payment_proof;
        return $proof ? get_public_file_url($proof, 'img/spmb/proofs') : null;
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

    public function getWhatsappMessageAttribute(): string
    {
        $schoolName = Setting::get('school_name', 'SDIT AL-FAHMI PALU');
        $portalUrl = route('spmb.dashboard.index');

        if ($this->isNeedRevision()) {
            $defaultTemplate = "Halo Bapak/Ibu orang tua/wali dari *{nama_siswa}*,\n\nKami dari panitia SPMB {sekolah} menginformasikan bahwa berkas pendaftaran ananda (No. Registrasi: *{no_daftar}*) memerlukan *perbaikan/revisi berkas*.\n\n*Catatan Panitia SPMB:*\n\"{catatan_revisi}\"\n\nMohon segera login ke akun SPMB Anda untuk mengunggah ulang berkas/foto yang sesuai di tautan berikut:\n{link_login}\n\nTerima kasih atas kerja samanya.";
            $template = Setting::get('spmb_wa_template_revision', $defaultTemplate);
        } elseif ($this->payment_status === 'paid' && $this->status === 'draft') {
            $defaultTemplate = "Halo Bapak/Ibu orang tua dari *{nama_siswa}*,\n\nKami dari panitia SPMB {sekolah} menginformasikan bahwa pembayaran uang pendaftaran ananda (No. Registrasi: *{no_daftar}*) telah berhasil terkonfirmasi lunas.\n\nNamun, formulir pendaftaran siswa tercatat masih berstatus *Draft (belum selesai diisi)*.\n\nMohon untuk segera login ke portal SPMB guna melengkapi formulir data diri dan mengunggah dokumen persyaratan di tautan berikut:\n{link_login}\n\nTerima kasih atas kerja samanya.";
            $template = Setting::get('spmb_wa_template_draft', $defaultTemplate);
        } else {
            $defaultTemplate = "Halo Bapak/Ibu orang tua dari *{nama_siswa}*,\n\nKami dari panitia SPMB {sekolah} menghubungi terkait pendaftaran calon siswa baru ananda dengan No. Registrasi: *{no_daftar}*.\n\nPortal SPMB: {link_login}\n\nTerima kasih.";
            $template = Setting::get('spmb_wa_template_general', $defaultTemplate);
        }

        if (empty(trim((string)$template))) {
            $template = $defaultTemplate;
        }

        $placeholders = [
            '{nama_siswa}' => $this->full_name,
            '{nama_orang_tua}' => $this->parent_name ?: 'Orang Tua / Wali',
            '{no_daftar}' => $this->registration_number,
            '{sekolah}' => $schoolName,
            '{gelombang}' => $this->wave?->name ?? 'Gelombang SPMB',
            '{catatan_revisi}' => $this->verification_notes ?: 'Mohon periksa dan unggah kembali kelengkapan berkas yang sesuai ketentuan.',
            '{link_login}' => $portalUrl,
            '{kontak_spmb}' => Setting::get('spmb_contact_phone', Setting::get('school_whatsapp', '')),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        $waPhone = $this->whatsapp_phone;
        if (!$waPhone) {
            return null;
        }

        return "https://wa.me/{$waPhone}?text=" . urlencode($this->whatsapp_message);
    }
}
