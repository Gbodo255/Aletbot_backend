<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Alert\StoreAlertRequest;
use App\Http\Requests\Api\Alert\UpdateAlertRequest;
use App\Models\Alert;
use App\Services\AlertService;
use App\Traits\CanLogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AlertController extends Controller
{
    use CanLogActivity;

    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Display a listing of alerts
     */
    public function index(Request $request): JsonResponse
    {
        // Check permission
        Gate::authorize('alerts.view');

        $query = Alert::with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('status') && in_array($request->status, ['pending', 'sent', 'failed'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('urgency') && in_array($request->urgency, ['Faible', 'Moyen', 'Critique'])) {
            $query->byUrgency($request->urgency);
        }

        if ($request->boolean('scheduled')) {
            $query->scheduled();
        }

        // Users can only see their own alerts unless they have admin permissions
        if (!Auth::user()->hasPermission('users.view')) {
            $query->where('user_id', Auth::id());
        }

        $alerts = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $alerts->items(),
            'meta' => [
                'pagination' => [
                    'total' => $alerts->total(),
                    'per_page' => $alerts->perPage(),
                    'current_page' => $alerts->currentPage(),
                    'last_page' => $alerts->lastPage(),
                    'from' => $alerts->firstItem(),
                    'to' => $alerts->lastItem(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created alert
     */
    public function store(StoreAlertRequest $request): JsonResponse
    {
        // Check permission
        Gate::authorize('alerts.create');

        $validated = $request->validated();

        $validated['user_id'] = Auth::id();
        $validated['type'] = $validated['type'] ?? 'Alerte';
        $validated['urgency_level'] = $validated['urgency_level'] ?? 'Moyen';
        $validated['channels'] = $validated['channels'] ?? ['telegram'];

        $alert = Alert::create($validated);

        // Schedule if needed, otherwise send immediately
        if ($alert->scheduled_at) {
            $this->alertService->scheduleAlert($alert);
        } else {
            $this->alertService->sendAlert($alert);
        }

        // Log activity using trait
        $this->logActivity('alert.created', "Alert from '{$alert->reporter_name}' created", 'Alert', $alert->id);

        return response()->json([
            'message' => 'Alerte créée avec succès',
            'data' => $alert->load('user:id,name,email')
        ], 201);
    }

    /**
     * Display the specified alert
     */
    public function show(Alert $alert): JsonResponse
    {
        // Check permission
        Gate::authorize('alerts.view');

        // Users can only see their own alerts unless they have admin permissions
        if (!Auth::user()->hasPermission('users.view') && $alert->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette alerte');
        }

        return response()->json([
            'data' => $alert->load('user:id,name,email')
        ]);
    }

    /**
     * Update the specified alert
     */
    public function update(UpdateAlertRequest $request, Alert $alert): JsonResponse
    {
        // Check permission
        Gate::authorize('alerts.edit');

        // Users can only edit their own alerts unless they have admin permissions
        if (!Auth::user()->hasPermission('users.view') && $alert->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette alerte');
        }

        // Cannot edit sent alerts
        if ($alert->status === 'sent') {
            return response()->json([
                'message' => 'Impossible de modifier une alerte déjà envoyée'
            ], 422);
        }

        $validated = $request->validated();
        $oldValues = $alert->toArray();
        
        $alert->update($validated);

        // Re-schedule if needed
        if ($alert->scheduled_at) {
            $this->alertService->scheduleAlert($alert);
        }

        // Log activity using trait
        $this->logActivity(
            'alert.updated', 
            "Alert from '{$alert->reporter_name}' updated", 
            'Alert', 
            $alert->id, 
            $oldValues, 
            $alert->fresh()->toArray()
        );

        return response()->json([
            'message' => 'Alerte mise à jour avec succès',
            'data' => $alert->load('user:id,name,email')
        ]);
    }

    /**
     * Remove the specified alert
     */
    public function destroy(Alert $alert): JsonResponse
    {
        // Check permission
        Gate::authorize('alerts.delete');

        // Users can only delete their own alerts unless they have admin permissions
        if (!Auth::user()->hasPermission('users.view') && $alert->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette alerte');
        }

        // Cannot delete sent alerts
        if ($alert->status === 'sent') {
            return response()->json([
                'message' => 'Impossible de supprimer une alerte déjà envoyée'
            ], 422);
        }

        $reporterName = $alert->reporter_name;
        $alertId = $alert->id;
        $alert->delete();

        // Log activity using trait
        $this->logActivity('alert.deleted', "Alert from '{$reporterName}' deleted", 'Alert', $alertId);

        return response()->json([
            'message' => 'Alerte supprimée avec succès'
        ]);
    }

    /**
     * Send an alert immediately
     */
    public function send(Alert $alert): JsonResponse
    {
        // Check permission
        Gate::authorize('alerts.send');

        // Users can only send their own alerts unless they have admin permissions
        if (!Auth::user()->hasPermission('users.view') && $alert->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette alerte');
        }

        // Cannot send already sent alerts
        if ($alert->status === 'sent') {
            return response()->json([
                'message' => 'Cette alerte a déjà été envoyée'
            ], 422);
        }

        $result = $this->alertService->sendAlert($alert);

        if ($result['status'] === 'success') {
            $this->logActivity('alert.sent', "Alert from '{$alert->reporter_name}' sent", 'Alert', $alert->id);
        }

        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Get alerts history
     */
    public function history(Request $request): JsonResponse
    {
        // Check permission
        Gate::authorize('alerts.view');

        $query = Alert::with('user:id,name,email')
            ->whereIn('status', ['sent', 'failed'])
            ->orderBy('sent_at', 'desc')
            ->orderBy('updated_at', 'desc');

        // Apply filters
        if ($request->has('start_date')) {
            $query->where('sent_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('sent_at', '<=', $request->end_date);
        }

        if ($request->has('urgency') && in_array($request->urgency, ['Faible', 'Moyen', 'Critique'])) {
            $query->byUrgency($request->urgency);
        }

        // Users can only see their own alerts unless they have admin permissions
        if (!Auth::user()->hasPermission('users.view')) {
            $query->where('user_id', Auth::id());
        }

        $alerts = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $alerts->items(),
            'meta' => [
                'pagination' => [
                    'total' => $alerts->total(),
                    'per_page' => $alerts->perPage(),
                    'current_page' => $alerts->currentPage(),
                    'last_page' => $alerts->lastPage(),
                    'from' => $alerts->firstItem(),
                    'to' => $alerts->lastItem(),
                ]
            ]
        ]);
    }
}
