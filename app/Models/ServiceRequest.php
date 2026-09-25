<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceRequestStatus;
use App\Models\Concerns\HasStatusHistory;
use App\Models\Concerns\HasTicketNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasFactory, HasStatusHistory, HasTicketNumber, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            if (empty($request->request_number)) {
                $serviceType = $request->serviceType ?? ServiceType::find($request->service_type_id);
                $prefix = $serviceType?->code ?? 'REQ';
                $request->request_number = self::generateTicketNumber($prefix);
            }

            if (empty($request->submitted_at)) {
                $request->submitted_at = now();
            }

            if (empty($request->status)) {
                $request->status = ServiceRequestStatus::SUBMITTED;
            }
        });
    }

    protected $fillable = [
        'request_number',
        'service_type_id',
        'submitter_id',
        'applicant_name',
        'applicant_nik',
        'family_card_number',
        'address',
        'village_id',
        'phone',
        'submitted_at',
        'officer_id',
        'work_unit_id',
        'status',
        'is_priority',
        'verification_result',
        'officer_notes',
        'assessment_notes',
        'service_result',
        'rejection_reason',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ServiceRequestStatus::class,
            'is_priority' => 'boolean',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ServiceRequestDocument::class);
    }

    public function dtsenCertificate(): HasOne
    {
        return $this->hasOne(DtsenCertificate::class);
    }

    public function pbiReactivation(): HasOne
    {
        return $this->hasOne(PbiReactivation::class);
    }

    public function rehabilitationCase(): HasOne
    {
        return $this->hasOne(RehabilitationCase::class);
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable')->latest('created_at');
    }

    public function dispositions(): MorphMany
    {
        return $this->morphMany(Disposition::class, 'dispositionable')->latest('disposed_at');
    }
}
