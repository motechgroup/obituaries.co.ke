<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use App\Models\BannerSize;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPlacementController extends Controller
{
    public function index()
    {
        $placements = AdPlacement::with('bannerSizes')->latest()->paginate(20);
        $bannerSizes = BannerSize::where('status', true)->get();

        return view('admin.advertising.placements.index', compact('placements', 'bannerSizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'page_type' => ['required', 'in:homepage,obituary,sidebar,search,category,county'],
            'description' => ['nullable', 'string', 'max:1000'],
            'banner_sizes' => ['required', 'array'],
            'banner_sizes.*' => ['exists:banner_sizes,id'],
        ]);

        $placement = AdPlacement::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'], '_'),
            'page_type' => $validated['page_type'],
            'description' => $validated['description'] ?? null,
            'status' => true,
        ]);

        $placement->bannerSizes()->sync($validated['banner_sizes']);

        return back()->with('success', "New ad placement slot '{$placement->name}' created successfully.");
    }
}
