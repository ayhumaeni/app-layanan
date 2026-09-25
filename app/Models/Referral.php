<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReferralStatus;
use App\Models\Concerns\HasStatusHistory;
use App\Models\Concerns\HasTicketNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Model
{
    use HasFactory, HasStatusHistory, HasTicketNumber, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $referral): void {
            if (empty($referral->referral_number)) {
                $referral->referral_number = self::generateTicketNumber('RJK');
            }

            if (empty($referral->referral_date)) {
                $referral->referral_date = now()->toDateString();
            }

            if (empty($referral->status)) {
                $referral->status = ReferralStatus::DRAFT;
            }
        });
    }

    protected $fillable = [
        'referral_number',
        'rehabilitation_case_id',
        'assessment_id',
        'referral_institution_id',
        'officer_id',
        'referral_date',
        'status',
        'service_result',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class,
            'referral_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function rehabilitationCase(): BelongsTo
    {
        return $this->belongsTo(RehabilitationCase::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(ReferralInstitution::class, 'referral_institution_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function monitoringRecords(): HasMany
    {
        return $this->hasMany(MonitoringRecord::class);
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable')->latest('created_at');
    }
}
