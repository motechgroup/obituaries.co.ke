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
        if (\App\Models\Setting::get('enable_public_submissions', '1') == '0') {
            return response()->view('obituaries.disabled', [], 403);
        }

        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        $relationships = ['Child', 'Spouse', 'Parent', 'Relative', 'Friend', 'Organization'];
        $categories = Obituary::CATEGORIES;

        return view('obituaries.submit', compact('counties', 'relationships', 'categories'));
    }

    public function store(Request $request)
    {
        if (\App\Models\Setting::get('enable_public_submissions', '1') == '0') {
            return redirect()->route('home')->with('error', 'Public obituary submissions are currently disabled by administration.');
        }

        if ($request->has('biography')) {
            $request->merge(['biography' => strip_tags($request->input('biography', ''))]);
        }

        $validated = $request->validate([
            // Step 1: Deceased Info
            'full_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', Rule::in(Obituary::CATEGORIES)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // 5MB max (PNG & JPEG/JPG only)
            'date_of_birth' => ['nullable', 'string'],
            'date_of_death' => ['nullable', 'string'],
            'county' => ['nullable', 'string', 'max:100'],
            'town' => ['nullable', 'string', 'max:100'],
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
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ], [
            'family_permission_confirmed.accepted' => 'You must confirm that you have permission from the family to submit this obituary.',
        ]);

        $dobParsed = $this->parseFlexibleDate($validated['date_of_birth'] ?? null);
        $dodParsed = $this->parseFlexibleDate($validated['date_of_death'] ?? null);

        if ($dobParsed && \Carbon\Carbon::parse($dobParsed)->isFuture()) {
            return back()->withInput()->withErrors(['date_of_birth' => 'Date of birth cannot be in the future.']);
        }
        if ($dodParsed && \Carbon\Carbon::parse($dodParsed)->isFuture()) {
            return back()->withInput()->withErrors(['date_of_death' => 'Date of death cannot be in the future.']);
        }

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
            'category' => $validated['category'] ?? 'Death Announcement',
            'full_name' => $validated['full_name'],
            'photo' => $photoPath,
            'gallery_images' => $galleryPaths,
            'date_of_birth' => $dobParsed,
            'date_of_death' => $dodParsed,
            'county' => $validated['county'] ?? null,
            'town' => $validated['town'] ?? null,
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

        \App\Services\FraudDetectionService::evaluateSubmission($request, $obituary);

        return redirect()->route('payments.checkout', $obituary->id)
            ->with('success', 'Obituary submitted successfully! Please complete payment to submit for verification.');
    }

    private function parseFlexibleDate(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        $input = trim($input);

        // 1. Year only e.g. "1945" or "2026"
        if (preg_match('/^\d{4}$/', $input)) {
            return $input . '-01-01';
        }

        // 2. Format YYYY-MM-DD or YYYY/MM/DD
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $input, $matches)) {
            $year = (int) $matches[1];
            $m = (int) $matches[2];
            $d = (int) $matches[3];

            if ($m > 12 && $d <= 12) {
                return sprintf('%04d-%02d-%02d', $year, $d, $m);
            }
            return sprintf('%04d-%02d-%02d', $year, max(1, min(12, $m)), max(1, min(31, $d)));
        }

        // 3. Format DD/MM/YYYY or MM/DD/YYYY or DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $input, $matches)) {
            $p1 = (int) $matches[1];
            $p2 = (int) $matches[2];
            $year = (int) $matches[3];

            if ($p1 > 12 && $p2 <= 12) {
                // p1 is Day (>12), p2 is Month (<=12) -> DD/MM/YYYY
                $day = $p1;
                $month = $p2;
            } elseif ($p2 > 12 && $p1 <= 12) {
                // p2 is Day (>12), p1 is Month (<=12) -> MM/DD/YYYY
                $day = $p2;
                $month = $p1;
            } else {
                // Both <= 12 (e.g. 15/04/1995 or 05/04/1995). Standard Kenyan/UK format is DD/MM/YYYY
                $day = $p1;
                $month = $p2;
            }

            return sprintf('%04d-%02d-%02d', $year, max(1, min(12, $month)), max(1, min(31, $day)));
        }

        try {
            return \Carbon\Carbon::parse($input)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $input;
        }
    }
}
