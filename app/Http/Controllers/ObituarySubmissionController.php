<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ObituarySubmissionController extends Controller
{
    public function create()
    {
        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        $relationships = [
            'Child', 'Spouse', 'Parent', 'Relative', 'Friend', 'Organization'
        ];

        return view('obituaries.submit', compact('counties', 'relationships'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1: Deceased Info
            'full_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB max
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'date_of_death' => ['required', 'date', 'after_or_equal:date_of_birth', 'before_or_equal:today'],
            'county' => ['required', 'string', 'max:100'],
            'town' => ['required', 'string', 'max:100'],
            'biography' => ['required', 'string', 'min:20'],

            // Step 2: Funeral Info
            'funeral_date' => ['nullable', 'date'],
            'burial_location' => ['nullable', 'string', 'max:255'],
            'church_service_location' => ['nullable', 'string', 'max:255'],
            'programme_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10MB max

            // Step 3: Submitter Info
            'submitter_name' => ['required', 'string', 'max:255'],
            'submitter_phone' => ['required', 'string', 'max:20'],
            'submitter_email' => ['nullable', 'email', 'max:255'],
            'relationship' => ['required', 'string', Rule::in(['Child', 'Spouse', 'Parent', 'Relative', 'Friend', 'Organization'])],
            'family_permission_confirmed' => ['accepted'],

            // Gallery Images (Moments in Time)
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'family_permission_confirmed.accepted' => 'You must confirm that you have permission from the family to submit this obituary.',
            'date_of_death.after_or_equal' => 'Date of death cannot be prior to date of birth.',
        ]);

        // Upload Profile Photo if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('obituaries/photos', 'public');
        }

        // Upload Gallery Images ("Moments in Time")
        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $galleryPaths[] = $file->store('obituaries/gallery', 'public');
                }
            }
        }

        // Upload Funeral Programme PDF if provided
        $programmePath = null;
        if ($request->hasFile('programme_file')) {
            $programmePath = $request->file('programme_file')->store('obituaries/programmes', 'public');
        }

        // Generate unique slug
        $slug = Obituary::generateUniqueSlug($validated['full_name']);

        // Create Pending Obituary Record
        $obituary = Obituary::create([
            'slug' => $slug,
            'full_name' => $validated['full_name'],
            'photo' => $photoPath,
            'gallery_images' => $galleryPaths,
            'date_of_birth' => $validated['date_of_birth'],
            'date_of_death' => $validated['date_of_death'],
            'county' => $validated['county'],
            'town' => $validated['town'],
            'biography' => $validated['biography'],
            'funeral_date' => $validated['funeral_date'] ?? null,
            'burial_location' => $validated['burial_location'] ?? null,
            'church_service_location' => $validated['church_service_location'] ?? null,
            'programme_file' => $programmePath,
            'submitter_name' => $validated['submitter_name'],
            'submitter_phone' => $validated['submitter_phone'],
            'submitter_email' => $validated['submitter_email'] ?? null,
            'relationship' => $validated['relationship'],
            'family_permission_confirmed' => true,
            'status' => 'pending_payment',
            'verification_status' => 'unverified',
        ]);

        return redirect()->route('payments.checkout', $obituary->id)
            ->with('success', 'Obituary submitted successfully! Please complete payment to submit for verification.');
    }
}
