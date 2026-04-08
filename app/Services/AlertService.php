<?php

namespace App\Services;

use App\Models\Alert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AlertService
{
    /**
     * Send an alert (simulation)
     *
     * @param Alert $alert
     * @return array
     */
    public function sendAlert(Alert $alert): array
    {
        try {
            $formattedMessage = $this->formatMessage($alert);

            // Log the alert in Laravel logs (simulation of sending)
            Log::info("TELEGRAM MESSAGE (SIMULATED):\n" . $formattedMessage);

            // Mark alert as sent
            $alert->markAsSent();

            // Log activity
            $this->logActivity($alert, 'alert.sent', "Alert from '{$alert->reporter_name}' sent successfully (Simulated)");

            return [
                'status' => 'success',
                'message' => 'Alerte envoyée (simulation Telegram)',
                'data' => [
                    'alert_id' => $alert->id,
                    'sent_at' => $alert->sent_at?->toISOString(),
                    'status' => $alert->status,
                    'message_preview' => substr($formattedMessage, 0, 100) . '...',
                ]
            ];

        } catch (\Exception $e) {
            // Mark alert as failed
            $alert->markAsFailed();

            // Log the error
            Log::error('ALERT SEND FAILED', [
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);

            // Log activity
            $this->logActivity($alert, 'alert.failed', "Alert from '{$alert->reporter_name}' failed to send: {$e->getMessage()}");

            return [
                'status' => 'error',
                'message' => 'Erreur lors de l\'envoi de l\'alerte',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format the alert message for Telegram (as requested in Mission 009)
     *
     * @param Alert $alert
     * @return string
     */
    private function formatMessage(Alert $alert): string
    {
        $priorityEmoji = match ($alert->urgency_level) {
            'Faible' => '🟢',
            'Moyen' => '🟡',
            'Critique' => '🔴',
            default => '⚪',
        };

        $typeEmoji = match ($alert->type) {
            'Urgence' => '⚡',
            'Information' => 'ℹ️',
            'Alerte' => '⚠️',
            'Autre' => '📁',
            default => '🔔',
        };

        $now = now()->format('d/m/Y à H\hi');
        $border = "────────────────";

        $message = "🔔 NOUVELLE ALERTE {$border}\n";
        $message .= "👤 {$alert->reporter_name}   {$typeEmoji} Type : {$alert->type}\n";
        $message .= "Priorité : " . strtoupper($alert->urgency_level) . " {$priorityEmoji}\n";
        $message .= "\n📝 {$alert->content}\n";
        $message .= "{$border}\n";
        $message .= "📅 Reçu le : {$now}";

        return $message;
    }

    /**
     * Schedule an alert for future sending
     *
     * @param Alert $alert
     * @return bool
     */
    public function scheduleAlert(Alert $alert): bool
    {
        if (!$alert->scheduled_at) {
            return false;
        }

        // Log activity
        $this->logActivity(
            $alert,
            'alert.scheduled',
            "Alert '{$alert->name}' scheduled for {$alert->scheduled_at->format('Y-m-d H:i:s')}"
        );

        return true;
    }

    /**
     * Process scheduled alerts that are due
     *
     * @return array
     */
    public function processScheduledAlerts(): array
    {
        $dueAlerts = Alert::pending()
            ->scheduled()
            ->where('scheduled_at', '<=', now())
            ->get();

        $results = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($dueAlerts as $alert) {
            $results['processed']++;

            $sendResult = $this->sendAlert($alert);

            if ($sendResult['status'] === 'success') {
                $results['sent']++;
            } else {
                $results['failed']++;
            }

            $results['details'][] = [
                'alert_id' => $alert->id,
                'reporter_name' => $alert->reporter_name,
                'result' => $sendResult
            ];
        }

        Log::info('SCHEDULED ALERTS PROCESSED', $results);

        return $results;
    }

    /**
     * Get alert statistics
     *
     * @param int|null $userId
     * @return array
     */
    public function getAlertStats(?int $userId = null): array
    {
        $query = Alert::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->pending()->count(),
            'sent' => (clone $query)->sent()->count(),
            'failed' => (clone $query)->failed()->count(),
            'scheduled' => (clone $query)->scheduled()->count(),
            'by_urgency' => [
                'Faible' => (clone $query)->byUrgency('Faible')->count(),
                'Moyen' => (clone $query)->byUrgency('Moyen')->count(),
                'Critique' => (clone $query)->byUrgency('Critique')->count(),
            ]
        ];
    }

    /**
     * Log activity for alert actions
     *
     * @param Alert $alert
     * @param string $action
     * @param string $description
     * @return void
     */
    private function logActivity(Alert $alert, string $action, string $description): void
    {
        // Import ActivityLog model
        $activityLog = new \App\Models\ActivityLog([
            'user_id' => Auth::id() ?? $alert->user_id,
            'action' => $action,
            'model' => 'Alert',
            'model_id' => $alert->id,
            'description' => $description,
            'old_values' => null,
            'new_values' => [
                'reporter_name' => $alert->reporter_name,
                'alert_status' => $alert->status,
                'alert_urgency' => $alert->urgency_level,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $activityLog->save();
    }
}