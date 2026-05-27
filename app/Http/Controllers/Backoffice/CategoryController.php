<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('backoffice.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|string'
        ]);

        Category::create([
            'name' => $request->name,
            'image_url' => $request->image_url
        ]);

        return redirect()->route('backoffice.categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|string'
        ]);

        $category->update([
            'name' => $request->name,
            'image_url' => $request->image_url
        ]);

        return redirect()->route('backoffice.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('backoffice.categories.index')->with('success', 'Category deleted successfully.');
    }
}
