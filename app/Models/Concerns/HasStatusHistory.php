<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\StatusHistory;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait HasStatusHistory
{
    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable')->latest('created_at');
    }

    public function recordStatusChange(string|BackedEnum $toStatus, ?string $notes = null, ?int $userId = null): void
    {
        $fromStatus = $this->status instanceof BackedEnum ? $this->status->value : (string) $this->status;
        $toStatusValue = $toStatus instanceof BackedEnum ? $toStatus->value : (string) $toStatus;

        $this->status = $toStatusValue;
        $this->save();

        $this->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatusValue,
            'notes' => $notes,
            'user_id' => $userId ?? Auth::id(),
            'created_at' => now(),
        ]);
    }
}
