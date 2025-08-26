<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        // Eager load asset count for each category
        $categories = Category::withCount('assets')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:categories,code',
            'name' => 'required|string|max:255|unique:categories,name',
        ]);
        
        try {
            Category::create($validated);
            return redirect()->route('categories.index')->with('success', 'Category added successfully!');
        } catch (\Exception $e) {
            \Log::error('Category creation failed: ' . $e->getMessage(), [
                'data' => $validated,
                'exception' => $e
            ]);
            return redirect()->back()->withInput()->with('error', 'Failed to add category. Please try again.');
        }
    }

    public function show(Category $category)
    {
        $assets = $category->assets()->with('location')->paginate(10);
        return view('categories.show', compact('category', 'assets'));
    }
}
