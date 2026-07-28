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

        return view('admin.obituaries.create', compact('counties'));
    }

    public function store(Request $request)
    {
        if ($request->filled('date_of_birth')) {
            $request->merge(['date_of_birth' => $this->parseFlexibleDate($request->input('date_of_birth'))]);
        }
        if ($request->filled('date_of_death')) {
            $request->merge(['date_of_death' => $this->parseFlexibleDate($request->input('date_of_death'))]);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'date_of_death' => ['required', 'date'],
            'county' => ['required', 'string'],
            'town' => ['required', 'string'],
            'biography' => ['required', 'string'],
            'funeral_date' => ['nullable', 'date'],
            'burial_location' => ['nullable', 'string', 'max:255'],
            'church_service_location' => ['nullable', 'string', 'max:255'],
            'submitter_name' => ['required', 'string', 'max:255'],
            'submitter_phone' => ['required', 'string', 'max:255'],
            'submitter_email' => ['nullable', 'email', 'max:255'],
            'relationship' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:published,draft,pending_verification'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'programme_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
        ]);

        // Auto generate unique slug
        $baseSlug = \Illuminate\Support\Str::slug($validated['full_name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Obituary::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $validated['slug'] = $slug;

        // Process Main Photo Upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = \App\Helpers\StorageHelper::savePublicFile($request->file('photo'), 'obituaries/photos');
        }

        // Process Gallery Images Upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $galleryPaths[] = \App\Helpers\StorageHelper::savePublicFile($file, 'obituaries/gallery');
                }
            }
            $validated['gallery_images'] = $galleryPaths;
        }

        // Process Programme File Upload
        if ($request->hasFile('programme_file')) {
            $validated['programme_file'] = \App\Helpers\StorageHelper::savePublicFile($request->file('programme_file'), 'obituaries/programmes');
        }

        // Allow rich formatting for Admin/Editor biography
        $validated['biography'] = \App\Helpers\StorageHelper::sanitizeHtml($validated['biography']);

        // Admin submissions auto-verify and skip payment completely
        $validated['verification_status'] = ($validated['status'] === 'published') ? 'verified' : 'pending';
        $validated['verified_by'] = Auth::guard('admin')->id();
        $validated['verified_at'] = now();

        $obituary = Obituary::create($validated);

        return redirect()->route('admin.obituaries.show', $obituary->id)
            ->with('success', "Obituary notice for '{$obituary->full_name}' created and published successfully by Admin (Payment Waived)!");
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

        $statuses = [
            'published' => 'Published',
            'pending_verification' => 'Pending Verification',
            'rejected' => 'Rejected',
            'pending_payment' => 'Pending Payment'
        ];

        return view('admin.obituaries.edit', compact('obituary', 'counties', 'statuses'));
    }

    public function update(Request $request, Obituary $obituary)
    {
        if ($request->filled('date_of_birth')) {
            $request->merge(['date_of_birth' => $this->parseFlexibleDate($request->input('date_of_birth'))]);
        }
        if ($request->filled('date_of_death')) {
            $request->merge(['date_of_death' => $this->parseFlexibleDate($request->input('date_of_death'))]);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
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
            'status' => ['required', 'string', 'in:published,pending_verification,rejected,pending_payment'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'programme_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = \App\Helpers\StorageHelper::savePublicFile($request->file('photo'), 'obituaries/photos');
        }

        if ($request->hasFile('gallery_images')) {
            $galleryPaths = is_array($obituary->gallery_images) ? $obituary->gallery_images : [];
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $galleryPaths[] = \App\Helpers\StorageHelper::savePublicFile($file, 'obituaries/gallery');
                }
            }
            $validated['gallery_images'] = array_values(array_unique($galleryPaths));
        }

        if ($request->hasFile('programme_file')) {
            $validated['programme_file'] = \App\Helpers\StorageHelper::savePublicFile($request->file('programme_file'), 'obituaries/programmes');
        }

        // Allow rich formatting for Admin/Editor biography
        $validated['biography'] = \App\Helpers\StorageHelper::sanitizeHtml($validated['biography']);

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

            // Dispatch Approval Email if email provided
            if ($obituary->submitter_email) {
                try {
                    $tmpl = \App\Models\Setting::get('mail_template_verification', "Dear {NAME},\n\nYour obituary notice for {DECEASED_NAME} has been published live.\n\nView Live: {LINK}");
                    $link = route('obituaries.show', $obituary->slug);
                    $body = str_replace(['{NAME}', '{DECEASED_NAME}', '{LINK}'], [$obituary->submitter_name, $obituary->full_name, $link], $tmpl);
                    \App\Services\MailService::sendHtmlEmail(
                        $obituary->submitter_email,
                        "Obituary Notice Verified & Published: {$obituary->full_name}",
                        $body,
                        $link,
                        'View Live Obituary Notice'
                    );
                } catch (\Throwable $e) {}
            }

            return back()->with('success', "Obituary for '{$obituary->full_name}' has been verified and published live!");
        } else {
            $obituary->update([
                'status' => 'rejected',
                'verification_status' => 'rejected',
                'verification_notes' => $notes,
                'verified_by' => Auth::guard('admin')->id(),
                'verified_at' => now(),
            ]);

            // Dispatch Rejection Email if email provided
            if ($obituary->submitter_email) {
                try {
                    $tmpl = \App\Models\Setting::get('mail_template_rejection', "Dear {NAME},\n\nYour obituary submission for {DECEASED_NAME} was not approved. Reason: {REASON}");
                    $body = str_replace(['{NAME}', '{DECEASED_NAME}', '{REASON}'], [$obituary->submitter_name, $obituary->full_name, $notes ?: 'Verification details incomplete.'], $tmpl);
                    \App\Services\MailService::sendHtmlEmail(
                        $obituary->submitter_email,
                        "Update on Obituary Submission: {$obituary->full_name}",
                        $body
                    );
                } catch (\Throwable $e) {}
            }

            return back()->with('success', "Obituary for '{$obituary->full_name}' has been marked as rejected.");
        }
    }

    public function unpublish(Request $request, Obituary $obituary)
    {
        $reason = $request->input('reason', 'Unpublished for moderation review.');
        
        $obituary->update([
            'status' => 'draft',
            'verification_status' => 'pending',
            'verification_notes' => $reason,
        ]);

        return back()->with('success', "Obituary notice for '{$obituary->full_name}' has been unpublished.");
    }

    public function destroy(Obituary $obituary)
    {
        $name = $obituary->full_name;
        $obituary->delete();

        return redirect()->route('admin.obituaries.index')
            ->with('success', "Obituary for '{$name}' has been deleted.");
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

        // 2. Format DD/MM/YYYY or DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $input, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }

        // 3. Format YYYY-MM-DD
        if (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $input)) {
            return $input;
        }

        try {
            return \Carbon\Carbon::parse($input)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $input;
        }
    }
}
