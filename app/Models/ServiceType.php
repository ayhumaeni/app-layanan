<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceRequestHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'handler',
        'needs_assessment',
        'sla_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'handler' => ServiceRequestHandler::class,
            'needs_assessment' => 'boolean',
            'sla_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ServiceRequirement::class)->orderBy('sort_order');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function informationPages(): HasMany
    {
        return $this->hasMany(InformationPage::class);
    }
}
