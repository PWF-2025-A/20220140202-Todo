<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('todos')
            ->where('user_id', Auth::id())
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        Category::create([
            'user_id' => Auth::id(),
            'title' => ucfirst($request->title),
        ]);

        return redirect()->route('category.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);

        // Check if the authenticated user is the owner
        if (Auth::id() !== $category->user_id) {
            return redirect()->route('category.index')->with('danger', 'You are not authorized to edit this category!');
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        // Check ownership
        if (Auth::id() !== $category->user_id) {
            return redirect()->route('category.index')->with('danger', 'You are not authorized to update this category!');
        }

        $request->validate([
            'title' => 'required|max:255',
        ]);

        $category->update([
            'title' => ucfirst($request->title),
        ]);

        return redirect()->route('category.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            return redirect()->route('category.index')->with('danger', 'You are not authorized to delete this category!');
        }

        $category->delete();

        return redirect()->route('category.index')->with('success', 'Category deleted successfully!');
    }
}
