<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'nik',
        'work_unit_id',
        'district_id',
        'village_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function submittedRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'submitter_id');
    }

    public function handledRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'officer_id');
    }

    public function signedCertificates(): HasMany
    {
        return $this->hasMany(DtsenCertificate::class, 'signer_id');
    }

    public function signedRecommendations(): HasMany
    {
        return $this->hasMany(PbiReactivation::class, 'signer_id');
    }

    public function rehabilitationCases(): HasMany
    {
        return $this->hasMany(RehabilitationCase::class, 'officer_id');
    }

    public function reportedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'reporter_id');
    }

    public function handledComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'officer_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class, 'user_id');
    }

    public function sentDispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'from_user_id');
    }

    public function receivedDispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'to_user_id');
    }

    public function informationPages(): HasMany
    {
        return $this->hasMany(InformationPage::class, 'manager_id');
    }
}
