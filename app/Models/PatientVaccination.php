<?php

namespace App\Models;

use Database\Factories\PatientVaccinationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientVaccination extends Model
{
    /** @use HasFactory<PatientVaccinationFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'vaccine_name',
        'dose',
        'administered_at',
        'administered_by',
        'lot_number',
        'notes',
        'recorded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'administered_at' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
