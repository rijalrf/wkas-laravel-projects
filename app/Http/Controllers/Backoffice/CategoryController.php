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
        $categories = Category::latest()->get();
        return view('backoffice.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'image_url' => $request->image_url
        ];

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $data['image_url'] = Storage::url($path);
        }

        Category::create($data);

        return redirect()->route('backoffice.categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'image_url' => $request->image_url
        ];

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $category->update($data);

        return redirect()->route('backoffice.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('backoffice.categories.index')->with('success', 'Category deleted successfully.');
    }
}
