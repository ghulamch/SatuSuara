<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    /**
     * Display a listing of administrative audit logs.
     */
    public function index(Request $request): View
    {
        $query = Activity::with('causer')->latest();

        if ($request->filled('category')) {
            $query->where('log_name', $request->query('category'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $categories = Activity::distinct()->pluck('log_name')->filter()->values();

        return view('admin.audit_logs.index', compact('logs', 'categories'));
    }
}

