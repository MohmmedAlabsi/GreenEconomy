<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::with([
            'feasibilityStudies',
            'knowledgeBaseItems'
        ])->findOrFail($id);

        return response()->json($category);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $category->update($request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}