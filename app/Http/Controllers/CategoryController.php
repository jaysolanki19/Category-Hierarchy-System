<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('order', 'asc')->get();
        $all_categories = Category::pluck('name', 'id')->all();
        return view('pages.categories.index', compact('categories', 'all_categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ]);
        if ($request->parent_id === null) {
            $new_order = 0;
            Category::whereNull('parent_id')
                ->where('order', '<=', $new_order)
                ->increment('order');
        } else {
            $last_order = Category::max('order');
            $new_order = $last_order === null ? 0 : $last_order + 1;
        }
        $category = new Category();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? null;
        $category->order = $new_order;
        $category->save();

        return redirect()->back()
            ->with('success', 'Category created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::with('parent', 'children')->findOrFail($id);
        return view('pages.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ]);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? null;
        $category->save();

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::where('id', $id)->first();
        if ($category) {
            $category->delete();
            return response()->json(['status' => 'success', 'message' => 'Category deleted succesfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function reorder(Request $request)
    {
        $categories = $request->get('categories');
        foreach ($categories as $index => $category_id) {
            $category = Category::find($category_id);
            $category->order = $index;
            $category->save();
        }
        return response()->json(['success' => true]);
    }
}
