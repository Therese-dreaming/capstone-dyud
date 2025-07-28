<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);
        try {
            Category::create($validated);
            return redirect()->route('categories.index')->with('success', 'Category added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to add category.');
        }
    }

    public function show(Category $category)
    {
        $assets = $category->assets()->with('location')->paginate(10);
        return view('categories.show', compact('category', 'assets'));
    }
}
