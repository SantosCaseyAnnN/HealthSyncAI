<?php

namespace App\Models;

use Database\Factories\LabOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabOrder extends Model
{
    /** @use HasFactory<LabOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'medical_record_id',
        'order_number',
        'test_name',
        'ordered_by',
        'status',
        'ordered_at',
        'result',
        'result_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'result_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
