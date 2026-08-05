<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = BusinessCategory::withCount(['profiles', 'campaigns'])->orderBy('sort_order')->get();
        return view('admin.advertising.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:business_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:100'],
        ]);

        BusinessCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'storefront',
            'status' => true,
            'sort_order' => BusinessCategory::max('sort_order') + 1,
        ]);

        return back()->with('success', "Business category '{$validated['name']}' created successfully.");
    }
}
