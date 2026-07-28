<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudAlert;
use App\Models\BlockedIp;
use App\Models\SecurityLog;
use App\Models\Obituary;
use Illuminate\Http\Request;

class FraudController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'open');

        $query = FraudAlert::with('obituary');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $alerts = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $blockedIps = BlockedIp::with('blocker')->orderBy('created_at', 'desc')->get();

        $totalAlerts = FraudAlert::count();
        $openAlerts = FraudAlert::where('status', 'open')->count();
        $criticalAlerts = FraudAlert::where('risk_level', 'CRITICAL')->where('status', 'open')->count();
        $totalBlockedIps = BlockedIp::count();

        return view('admin.fraud_alerts.index', compact('alerts', 'blockedIps', 'totalAlerts', 'openAlerts', 'criticalAlerts', 'totalBlockedIps', 'status'));
    }

    public function dismiss(FraudAlert $alert)
    {
        $alert->update(['status' => 'dismissed']);

        if ($alert->obituary) {
            $alert->obituary->update([
                'is_flagged_fraud' => false,
                'fraud_reason' => null,
            ]);
        }

        SecurityLog::log('fraud_alert_dismissed', 'info', $alert->obituary_id, "Fraud alert #{$alert->id} marked as safe/dismissed by admin.");

        return back()->with('success', 'Fraud alert has been dismissed and marked as safe.');
    }

    public function blockIpAndUnpublish(Request $request, FraudAlert $alert)
    {
        $ip = $alert->ip_address;

        BlockedIp::firstOrCreate([
            'ip_address' => $ip,
        ], [
            'reason' => "Fraud Threat: {$alert->threat_type} ({$alert->description})",
            'blocked_by' => auth('admin')->id(),
        ]);

        if ($alert->obituary) {
            $alert->obituary->update([
                'status' => 'draft',
                'verification_status' => 'rejected',
                'verification_notes' => "Unpublished due to automated fraud threat detection: {$alert->threat_type}",
            ]);
        }

        $alert->update(['status' => 'blocked']);

        SecurityLog::log('ip_blocked_and_unpublished', 'critical', $alert->obituary_id, "IP {$ip} blocked and obituary #{$alert->obituary_id} unpublished.");

        return back()->with('success', "IP address '{$ip}' blocked and linked obituary unpublished successfully.");
    }

    public function unblockIp(BlockedIp $ip)
    {
        $ipAddress = $ip->ip_address;
        $ip->delete();

        SecurityLog::log('ip_unblocked', 'info', null, "IP address {$ipAddress} unblocked by admin.");

        return back()->with('success', "IP address '{$ipAddress}' has been removed from the blacklist.");
    }
}
