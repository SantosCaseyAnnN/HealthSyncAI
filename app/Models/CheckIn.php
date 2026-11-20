<?php

namespace App\Models;

use Database\Factories\CheckInFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    /** @use HasFactory<CheckInFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'possible_match_patient_id',
        'existing_patient_number',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'phone',
        'email',
        'address',
        'consent_given',
        'reason_for_visit',
        'gender',
        'height_cm',
        'weight_kg',
        'blood_pressure',
        'temperature_c',
        'pulse_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'status',
        'check_in_date',
        'queue_number',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'check_in_date' => 'date',
            'reviewed_at' => 'datetime',
            'height_cm' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'temperature_c' => 'decimal:1',
            'oxygen_saturation' => 'decimal:2',
            'consent_given' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function possibleMatchPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'possible_match_patient_id');
    }

    public static function linkPendingDuplicates(Patient $patient, ?int $exceptCheckInId = null): void
    {
        if ($patient->date_of_birth === null || blank($patient->middle_name)) {
            return;
        }

        static::query()
            ->where('status', 'pending')
            ->whereNull('patient_id')
            ->whereNull('possible_match_patient_id')
            ->when($exceptCheckInId !== null, fn ($query) => $query->where('id', '!=', $exceptCheckInId))
            ->whereDate('date_of_birth', $patient->date_of_birth->toDateString())
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [mb_strtolower(trim($patient->first_name))])
            ->whereRaw('LOWER(TRIM(middle_name)) = ?', [mb_strtolower(trim($patient->middle_name))])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [mb_strtolower(trim($patient->last_name))])
            ->update(['possible_match_patient_id' => $patient->id]);
    }

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            trim((string) $this->first_name),
            trim((string) $this->middle_name),
            trim((string) $this->last_name),
        ], fn (string $name): bool => $name !== ''));
    }

    public function requestedPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'existing_patient_number', 'patient_number');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
