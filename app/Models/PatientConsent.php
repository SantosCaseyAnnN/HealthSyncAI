<?php

namespace App\Models;

use Database\Factories\PatientConsentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientConsent extends Model
{
    /** @use HasFactory<PatientConsentFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'consent_type',
        'form_version',
        'status',
        'recorded_at',
        'recorded_by_user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
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
