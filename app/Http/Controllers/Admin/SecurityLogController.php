<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Models\BlockedIp;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    public function index(Request $request)
    {
        $searchIp = trim((string)$request->input('ip'));
        $action = trim((string)$request->input('action'));
        $severity = trim((string)$request->input('severity'));

        $query = SecurityLog::with(['obituary', 'admin']);

        if (!empty($searchIp)) {
            $query->where('ip_address', 'like', "%{$searchIp}%");
        }

        if (!empty($action)) {
            $query->where('action', $action);
        }

        if (!empty($severity)) {
            $query->where('severity', $severity);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        $totalLogs = SecurityLog::count();
        $warningCount = SecurityLog::whereIn('severity', ['warning', 'danger', 'critical'])->count();
        $uniqueIps = SecurityLog::distinct('ip_address')->count('ip_address');

        return view('admin.security_logs.index', compact('logs', 'totalLogs', 'warningCount', 'uniqueIps', 'searchIp', 'action', 'severity'));
    }

    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => ['required', 'ip'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $ip = $request->input('ip_address');
        $reason = $request->input('reason', 'Blocked via Security Logs moderation panel.');

        BlockedIp::firstOrCreate([
            'ip_address' => $ip,
        ], [
            'reason' => $reason,
            'blocked_by' => auth('admin')->id(),
        ]);

        SecurityLog::log('ip_blocked', 'critical', null, "IP address {$ip} was blocked by admin. Reason: {$reason}");

        return back()->with('success', "IP Address '{$ip}' has been added to the blacklist.");
    }
}
