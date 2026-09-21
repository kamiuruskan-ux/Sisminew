<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'nik',
        'gender',
        'religion',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'email',
        'class_id',
        'major_id',
        'student_status',
        'entry_year',
        'graduation_year',
        'alumni_notes',
        'parent_name',
        'father_name',
        'mother_name',
        'parent_phone',
        'parent_job',
        'parent_address',
        'photo',
        'qr_code',
        'spp_discount',
        'discount_description',
        'savings_balance',
        'pin',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'spp_discount' => 'integer',
        'savings_balance' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->qr_code)) {
                $student->qr_code = self::generateUniqueQrCode();
            }
        });
    }

    private static function generateUniqueQrCode(): string
    {
        do {
            $qrCode = 'STD-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
        } while (self::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }

    public function getQrCodeDataAttribute(): string
    {
        return json_encode([
            'student_id' => $this->id,
            'nisn' => $this->nisn,
            'name' => $this->user?->name,
            'qr_code' => $this->qr_code,
        ]);
    }

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? 'Siswa';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function paymentBills(): HasMany
    {
        return $this->hasMany(StudentPaymentBill::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function spmbRegistration(): HasMany
    {
        return $this->hasMany(SpmbRegistration::class, 'user_id', 'user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(BkStudentViolation::class, 'student_id');
    }

    public function halaqahRecords(): HasMany
    {
        return $this->hasMany(HalaqahRecord::class, 'student_id')->latest('assessment_date');
    }

    public function halaqahMember(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(QuranHalaqahMember::class, 'student_id');
    }


    public function getAttendancePercentageAttribute(): ?float
    {
        $total = $this->attendances()->count();
        if ($total === 0) return null;

        $present = $this->attendances()
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($present / $total) * 100, 2);
    }

    public function savingsTransactions(): HasMany
    {
        return $this->hasMany(SavingsTransaction::class)->latest();
    }

    public function getFormattedSavingsBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->savings_balance ?? 0, 0, ',', '.');
    }

    public function getAverageGradeAttribute(): ?float
    {
        return $this->grades()
            ->selectRaw('AVG(score) as average')
            ->value('average');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        if (Str::startsWith($this->photo, ['http://', 'https://'])) {
            return $this->photo;
        }
        
        $filename = basename($this->photo);
        return asset('img/students/' . $filename);
    }
}
