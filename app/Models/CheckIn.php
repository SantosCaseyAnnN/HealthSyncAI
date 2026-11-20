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
        'first_name',
        'last_name',
        'date_of_birth',
        'phone',
        'email',
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
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
