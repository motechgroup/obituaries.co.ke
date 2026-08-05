<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Services\MailService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCampaignController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = AdCampaign::with(['advertiser', 'businessProfile', 'placement', 'bannerSize'])
            ->withCount(['impressions', 'clicks']);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $campaigns = $query->latest()->paginate(20);

        $counts = [
            'all' => AdCampaign::count(),
            'pending_approval' => AdCampaign::where('status', 'pending_approval')->count(),
            'running' => AdCampaign::where('status', 'running')->count(),
            'payment_pending' => AdCampaign::where('status', 'payment_pending')->count(),
            'rejected' => AdCampaign::where('status', 'rejected')->count(),
            'expired' => AdCampaign::where('status', 'expired')->count(),
        ];

        return view('admin.advertising.campaigns.index', compact('campaigns', 'status', 'counts'));
    }

    public function show(AdCampaign $campaign)
    {
        $campaign->load(['advertiser', 'businessProfile', 'placement', 'bannerSize', 'category', 'counties', 'payments']);
        $campaign->loadCount(['impressions', 'clicks']);

        return view('admin.advertising.campaigns.show', compact('campaign'));
    }

    public function approve(AdCampaign $campaign)
    {
        $admin = Auth::guard('admin')->user();

        $campaign->update([
            'status' => 'running',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        // Send Notification Email & SMS
        $advertiser = $campaign->advertiser;
        if ($advertiser) {
            $msg = "Dear {$advertiser->contact_person}, your ad campaign '{$campaign->name}' on Obituaries.co.ke has been APPROVED and is now live!";
            SmsService::sendSms($advertiser->phone_number, $msg);
            MailService::sendEmail($advertiser->email, "Ad Campaign Approved: {$campaign->name}", $msg);
        }

        return back()->with('success', "Campaign '{$campaign->name}' has been APPROVED and is now live!");
    }

    public function reject(Request $request, AdCampaign $campaign)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $campaign->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $advertiser = $campaign->advertiser;
        if ($advertiser) {
            $msg = "Dear {$advertiser->contact_person}, your ad campaign '{$campaign->name}' was not approved. Reason: {$validated['rejection_reason']}";
            SmsService::sendSms($advertiser->phone_number, $msg);
            MailService::sendEmail($advertiser->email, "Ad Campaign Update: {$campaign->name}", $msg);
        }

        return back()->with('success', "Campaign '{$campaign->name}' has been REJECTED.");
    }

    public function pause(AdCampaign $campaign)
    {
        $campaign->update(['status' => 'approved']);
        return back()->with('success', "Campaign '{$campaign->name}' has been PAUSED.");
    }

    public function resume(AdCampaign $campaign)
    {
        $campaign->update(['status' => 'running']);
        return back()->with('success', "Campaign '{$campaign->name}' has been RESUMED.");
    }

    public function destroy(AdCampaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('admin.advertising.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
