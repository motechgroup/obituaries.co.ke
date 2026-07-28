<?php

namespace App\Services;

use App\Models\Obituary;
use App\Models\FraudAlert;
use App\Models\SecurityLog;
use App\Models\BlockedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FraudDetectionService
{
    /**
     * Evaluates security and fraud risk for a new or updated obituary submission.
     */
    public static function evaluateSubmission(Request $request, Obituary $obituary): void
    {
        $ip = $request->ip() ?: '127.0.0.1';
        $agent = $request->header('User-Agent', '');

        // Determine device type
        $deviceType = 'Desktop';
        if (preg_match('/(android|bb\d+|meego).+mobile|avail|blackberry|emulator|iphone|ipod|sm-t|gt-p|opera m(obi|ini)|palmsource|pocket|tablet|windows phone/i', $agent)) {
            $deviceType = preg_match('/tablet|ipad|playbook|silk/i', $agent) ? 'Tablet' : 'Mobile';
        }

        // Store IP and Device details on obituary record
        $obituary->update([
            'ip_address' => $ip,
            'user_agent' => Str::limit($agent, 500, ''),
            'device_type' => $deviceType,
        ]);

        // Calculate Threat Risk Score
        $riskScore = 0;
        $reasons = [];

        // 1. High Frequency Check: >3 submissions from same IP in last 15 mins
        $recentCount = Obituary::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentCount >= 3) {
            $riskScore += 45;
            $reasons[] = "High Submission Frequency ({$recentCount} posts in 15 minutes)";
        }

        // 2. Multiple Identities Check: >2 different submitter names or phones from same IP in 24 hrs
        $distinctNames = Obituary::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHours(24))
            ->distinct('submitter_name')
            ->count('submitter_name');

        if ($distinctNames > 2) {
            $riskScore += 35;
            $reasons[] = "Multiple Submitter Identities ({$distinctNames} names used from IP)";
        }

        // 3. Spam / Scam Keyword Filter
        $spamKeywords = [
            'wire transfer', 'send money', 'bitcoin', 'crypto', 'loan offer', 'whatsapp group',
            'telegram link', 'claim inheritance', 'bank transfer', 'betting tip', 'casin0',
            'http://', 'https://', 'www.', '.com/', 'cheap price', 'click here'
        ];

        $contentToScan = Str::lower($obituary->full_name . ' ' . $obituary->biography . ' ' . $obituary->submitter_name);
        foreach ($spamKeywords as $kw) {
            if (str_contains($contentToScan, $kw)) {
                $riskScore += 40;
                $reasons[] = "Suspicious Keyword Detected ('{$kw}')";
                break;
            }
        }

        // Flag Fraud if risk score >= 40
        if ($riskScore >= 40) {
            $reasonText = implode('; ', $reasons);

            $obituary->update([
                'is_flagged_fraud' => true,
                'fraud_reason' => $reasonText,
            ]);

            $riskLevel = $riskScore >= 70 ? 'CRITICAL' : ($riskScore >= 50 ? 'HIGH' : 'MEDIUM');

            FraudAlert::create([
                'ip_address' => $ip,
                'obituary_id' => $obituary->id,
                'risk_score' => min($riskScore, 100),
                'risk_level' => $riskLevel,
                'threat_type' => $reasons[0] ?? 'Suspicious Activity Pattern',
                'description' => "Obituary #{$obituary->id} ('{$obituary->full_name}') submitted by {$obituary->submitter_name} ({$obituary->submitter_phone}). Risk Reasons: {$reasonText}",
                'status' => 'open',
            ]);

            SecurityLog::log('fraud_pattern_detected', 'danger', $obituary->id, "Fraud Threat Flagged: {$reasonText}");
        } else {
            SecurityLog::log('obituary_submitted', 'info', $obituary->id, "Obituary submitted by {$obituary->submitter_name} ({$deviceType} / {$ip})");
        }
    }
}
