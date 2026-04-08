<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_name',
        'content',
        'type',
        'urgency_level',
        'channels',
        'status',
        'scheduled_at',
        'sent_at',
        'recurrence',
        'user_id',
    ];

    protected $casts = [
        'channels' => 'array',
        'recurrence' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at');
    }

    public function scopeByUrgency($query, $level)
    {
        return $query->where('urgency_level', $level);
    }

    // Helper methods
    public function isRecurring(): bool
    {
        return !empty($this->recurrence);
    }

    public function shouldSendNow(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if ($this->scheduled_at) {
            return Carbon::now()->gte($this->scheduled_at);
        }

        return true; // Send immediately if no scheduled_at
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function getUrgencyColor(): string
    {
        return match ($this->urgency_level) {
            'Faible' => 'green',
            'Moyen' => 'yellow',
            'Critique' => 'red',
            // Keep old values just in case during transition
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'pending' => '⏳ En attente',
            'sent' => '✅ Envoyée',
            'failed' => '❌ Échouée',
            default => '❓ Inconnue',
        };
    }
}
