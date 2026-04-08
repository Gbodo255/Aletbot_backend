<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityLogController
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'logs' => $logs,
        ], Response::HTTP_OK);
    }

    public function userActivity(Request $request, $userId)
    {
        // Allow users to see only their own activity, admins can see any
        if (!$request->user()->isAdmin() && (int)$userId !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $query = ActivityLog::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(50);

        return response()->json([
            'logs' => $logs,
        ], Response::HTTP_OK);
    }

    public function show(Request $request, ActivityLog $log)
    {
        if (!$request->user()->isAdmin() && $log->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'log' => $log->load('user'),
        ], Response::HTTP_OK);
    }
}

