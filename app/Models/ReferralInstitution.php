<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralInstitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // panti, balai, RS, LKS
        'address',
        'contact',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }
}
