<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'subject'])
            ->latest('created_at');

        // Search berdasarkan action, deskripsi, atau pengguna
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan jenis aktivitas
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter berdasarkan pengguna
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan tanggal mulai
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(15)->withQueryString();

        $users = \App\Models\User::orderBy('name')->get();

        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('administrator.audit-logs.index', compact(
            'auditLogs',
            'users',
            'actions'
        ));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'subject']);

        return view('administrator.audit-logs.show', compact(
            'auditLog'
        ));
    }
}
