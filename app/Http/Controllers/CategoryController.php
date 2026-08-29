<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json($categories);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::with([
            'feasibilityStudies',
            'knowledgeBaseItems'
        ])->findOrFail($id);

        return response()->json($category);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'type' => 'required|string|max:50',
        ]);


        $category = Category::create($validated);


        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return response()->json($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);


        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $id,
            'type' => 'sometimes|string|max:50',
        ]);


        $category->update($validated);


        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();


        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
