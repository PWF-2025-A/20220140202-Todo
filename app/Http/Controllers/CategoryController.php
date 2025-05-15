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
            ->where('user_id', auth()->id())
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
        $request->validate(['title' => 'required']);
        Category::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
        ]);
        //return redirect()->route('category.index');
        return redirect()->route('category.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id); // Ambil kategori berdasarkan ID
         // Check if the authenticated user is the owner of the category
        if (auth()->user()->id == $category->user_id) {
        // Proceed with the edit if the user is authorized
        return view('categories.edit', compact('category'));
        } else {
        // Redirect with an error message if the user is not authorized
        return redirect()->route('category.index')->with('danger', 'You are not authorized to edit this category!');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Ambil kategori berdasarkan ID
    $category = Category::findOrFail($id);
          // Validasi data
    $request->validate([
        'title' => 'required|max:255',
    ]);

    // Update kategori
    $category->update([
        'title' => ucfirst($request->title),
    ]);

    // Redirect dengan pesan sukses
    return redirect()->route('category.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     $category->delete();
    //     return redirect()->route('categories.index');
    // }
    public function destroy(Category $category)
{
    // Cek apakah pengguna yang sedang login adalah pemilik kategori ini
    if (auth()->user()->id == $category->user_id) {
        // Jika ya, hapus kategori
        $category->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('category.index')->with('success', 'Category deleted successfully!');
    } else {
        // Jika tidak, kembalikan ke halaman dengan pesan error
        return redirect()->route('category.index')->with('danger', 'You are not authorized to delete this category!');
    }
}

}