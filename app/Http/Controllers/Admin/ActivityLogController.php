<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller {
    public function index(Request $request): View {
        $query = ActivityLog::with('causer');

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->integer('causer_id'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('description', 'like', '%'.$search.'%')
                    ->orWhere('event', 'like', '%'.$search.'%')
                    ->orWhere('route_name', 'like', '%'.$search.'%')
                    ->orWhere('subject_type', 'like', '%'.$search.'%');
            });
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'created' => ActivityLog::where('event', 'created')->count(),
            'updated' => ActivityLog::where('event', 'updated')->count(),
            'deleted' => ActivityLog::where('event', 'deleted')->count(),
        ];

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'stats' => $stats,
            'events' => ActivityLog::query()->select('event')->distinct()->orderBy('event')->pluck('event'),
            'subjectTypes' => ActivityLog::query()->select('subject_type')->whereNotNull('subject_type')->distinct()->orderBy('subject_type')->pluck('subject_type'),
            'admins' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(ActivityLog $activityLog): View {
        $activityLog->load('causer', 'subject');

        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
