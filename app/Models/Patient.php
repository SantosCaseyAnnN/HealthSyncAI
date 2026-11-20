<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_number',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'provider',
        'status',
        'blood_type',
        'allergies',
        'medical_history',
        'family_history',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'address',
        'height_cm',
        'weight_kg',
        'bmi',
        'blood_pressure',
        'temperature_c',
        'pulse_rate',
        'respiratory_rate',
        'oxygen_saturation',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'height_cm' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'bmi' => 'decimal:2',
            'temperature_c' => 'decimal:1',
            'oxygen_saturation' => 'decimal:2',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            trim((string) $this->first_name),
            trim((string) $this->middle_name),
            trim((string) $this->last_name),
        ], fn (string $name): bool => $name !== ''));
    }

    public function scopeMatchingIdentity(
        Builder $query,
        string $firstName,
        string $middleName,
        string $lastName,
        string $dateOfBirth,
    ): void {
        $query
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [mb_strtolower(trim($firstName))])
            ->where(function (Builder $query) use ($middleName): void {
                $query
                    ->whereRaw('LOWER(TRIM(middle_name)) = ?', [mb_strtolower(trim($middleName))])
                    ->orWhereNull('middle_name')
                    ->orWhere('middle_name', '');
            })
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [mb_strtolower(trim($lastName))])
            ->whereDate('date_of_birth', $dateOfBirth);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function latestMedicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class)->latestOfMany('recorded_at');
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(PatientVaccination::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(PatientConsent::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
