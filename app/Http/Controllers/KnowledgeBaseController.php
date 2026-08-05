<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBaseItem;
use Illuminate\Routing\Controller;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = KnowledgeBaseItem::with(['category', 'user'])->where('status', 'published')->latest()->paginate(12);
        return response()->json($items);
    }

    // عرض مقال أو عنصر محدد
    public function show($id)
    {
        // زيادة عدد المشاهدات عند القراءة
        //$knowledgeBase->increment('view_count');
        $KnowledgeBaseItem_id = KnowledgeBaseItem::with(['category', 'user'])->findOrFail($id);
        return response()->json($KnowledgeBaseItem_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return response()->json([
        'message' => 'Create knowledge base item'
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Store a newly created resource.
 */
public function store(Request $request)
{
    $validated = $request->validate([

        'title' => 'required|string|max:255',

        'summary' => 'nullable|string',

        'content' => 'nullable|string',

        'type' => 'required|string|max:50',

        'status' => 'nullable|string|max:50',

        'category_id' => 'required|exists:categories,id',

        'media_url' => 'nullable|string|max:255',

        'file_size_bytes' => 'nullable|integer',

        'view_count' => 'nullable|integer',

        'user_id' => 'required|exists:users,id',

    ]);

    $item = KnowledgeBaseItem::create($validated);

    return response()->json([

        'message' => 'Knowledge base item created successfully',

        'data' => $item

    ], 201);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    return response()->json([
        'message' => 'Edit knowledge base item',
        'id' => $id
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource.
 */
public function update(Request $request, string $id)
{
    $item = KnowledgeBaseItem::findOrFail($id);

    $validated = $request->validate([

        'title' => 'sometimes|string|max:255',

        'summary' => 'nullable|string',

        'content' => 'nullable|string',

        'type' => 'sometimes|string|max:50',

        'status' => 'nullable|string|max:50',

        'category_id' => 'sometimes|exists:categories,id',

        'media_url' => 'nullable|string|max:255',

        'file_size_bytes' => 'nullable|integer',

        'view_count' => 'nullable|integer',

        'user_id' => 'sometimes|exists:users,id',

    ]);

    $item->update($validated);

    return response()->json([

        'message' => 'Knowledge base item updated successfully',

        'data' => $item

    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource.
 */
public function destroy(string $id)
{
    $item = KnowledgeBaseItem::findOrFail($id);

    $item->delete();

    return response()->json([

        'message' => 'Knowledge base item deleted successfully'

    ]);
}
}
