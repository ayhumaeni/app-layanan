<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MinistryDecision;
use App\Enums\PbiReactivationReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbiReactivation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_request_id',
        'participant_name',
        'participant_nik',
        'bpjs_card_number',
        'deactivated_date',
        'reason',
        'health_facility_name',
        'health_letter_number',
        'decile',
        'eligibility_notes',
        'recommendation_number',
        'recommendation_issued_at',
        'signer_id',
        'proposed_to_ministry_at',
        'ministry_decision',
        'ministry_decided_at',
        'reactivated_date',
    ];

    protected function casts(): array
    {
        return [
            'reason' => PbiReactivationReason::class,
            'ministry_decision' => MinistryDecision::class,
            'decile' => 'integer',
            'deactivated_date' => 'date',
            'reactivated_date' => 'date',
            'recommendation_issued_at' => 'datetime',
            'proposed_to_ministry_at' => 'datetime',
            'ministry_decided_at' => 'datetime',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable')->orderBy('step');
    }
}
