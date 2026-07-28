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
        // Parse flexible Date of Birth (e.g. DD/MM/YYYY, DD-MM-YYYY, or YYYY)
        if ($request->filled('date_of_birth')) {
            $dobInput = trim($request->input('date_of_birth'));
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dobInput, $matches)) {
                $dobInput = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            } elseif (preg_match('/^\d{4}$/', $dobInput)) {
                $dobInput = $dobInput . '-01-01';
            }
            $request->merge(['date_of_birth' => $dobInput]);
        }

        if ($request->has('biography')) {
            $request->merge(['biography' => strip_tags($request->input('biography', ''))]);
        }

        $validated = $request->validate([
            // Step 1: Deceased Info
            'full_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB max
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'date_of_death' => ['required', 'date', 'before_or_equal:today'],
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
        ]);

        // Upload Profile Photo if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = \App\Helpers\StorageHelper::savePublicFile($request->file('photo'), 'obituaries/photos');
        }

        // Upload Gallery Images ("Moments in Time")
        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $galleryPaths[] = \App\Helpers\StorageHelper::savePublicFile($file, 'obituaries/gallery');
                }
            }
        }

        // Upload Funeral Programme PDF if provided
        $programmePath = null;
        if ($request->hasFile('programme_file')) {
            $programmePath = \App\Helpers\StorageHelper::savePublicFile($request->file('programme_file'), 'obituaries/programmes');
        }

        // Generate unique slug
        $slug = Obituary::generateUniqueSlug($validated['full_name']);

        // Create Pending Obituary Record
        $obituary = Obituary::create([
            'slug' => $slug,
            'full_name' => $validated['full_name'],
            'photo' => $photoPath,
            'gallery_images' => $galleryPaths,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'date_of_death' => $validated['date_of_death'],
            'county' => $validated['county'],
            'town' => $validated['town'],
            'biography' => strip_tags($validated['biography']),
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
