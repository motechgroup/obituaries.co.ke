<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obituary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObituaryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Obituary::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('submitter_name', 'like', "%{$search}%")
                  ->orWhere('submitter_phone', 'like', "%{$search}%");
            });
        }

        $obituaries = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.obituaries.index', compact('obituaries', 'status', 'search'));
    }

    public function show(Obituary $obituary)
    {
        $obituary->load(['payments', 'verifier']);
        return view('admin.obituaries.show', compact('obituary'));
    }

    public function edit(Obituary $obituary)
    {
        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        return view('admin.obituaries.edit', compact('obituary', 'counties'));
    }

    public function update(Request $request, Obituary $obituary)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'date_of_death' => ['required', 'date'],
            'county' => ['required', 'string'],
            'town' => ['required', 'string'],
            'biography' => ['required', 'string'],
            'funeral_date' => ['nullable', 'date'],
            'burial_location' => ['nullable', 'string'],
            'church_service_location' => ['nullable', 'string'],
            'submitter_name' => ['required', 'string'],
            'submitter_phone' => ['required', 'string'],
            'submitter_email' => ['nullable', 'email'],
            'relationship' => ['required', 'string'],
        ]);

        $obituary->update($validated);

        return redirect()->route('admin.obituaries.show', $obituary->id)
            ->with('success', 'Obituary details updated successfully.');
    }

    public function verify(Request $request, Obituary $obituary)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $action = $request->input('action');
        $notes = $request->input('verification_notes');

        if ($action === 'approve') {
            $obituary->update([
                'status' => 'published',
                'verification_status' => 'verified',
                'verification_notes' => $notes,
                'verified_by' => Auth::guard('admin')->id(),
                'verified_at' => now(),
            ]);

            return back()->with('success', "Obituary for '{$obituary->full_name}' has been verified and published live!");
        } else {
            $obituary->update([
                'status' => 'rejected',
                'verification_status' => 'rejected',
                'verification_notes' => $notes,
                'verified_by' => Auth::guard('admin')->id(),
                'verified_at' => now(),
            ]);

            return back()->with('success', "Obituary for '{$obituary->full_name}' has been marked as rejected.");
        }
    }

    public function destroy(Obituary $obituary)
    {
        $name = $obituary->full_name;
        $obituary->delete();

        return redirect()->route('admin.obituaries.index')
            ->with('success', "Obituary for '{$name}' has been deleted.");
    }
}
