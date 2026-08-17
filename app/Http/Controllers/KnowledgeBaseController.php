<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBaseItem;
use Illuminate\Routing\Controller;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $items = KnowledgeBaseItem::with(['category', 'user'])->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function show($id)
    {
        $item = KnowledgeBaseItem::with(['category', 'user'])->findOrFail($id);
        return response()->json($item);
    }

    public function create()
    {
        return response()->json(['message' => 'Create knowledge base item']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'summary'         => 'nullable|string',
            'content'         => 'nullable|string',
            'type'            => 'required|string|max:50',
            'status'          => 'nullable|string|max:50',
            'category_id'     => 'required|exists:categories,id',
            'media_url'       => 'nullable|string|max:255',
            'file_size_bytes' => 'nullable|integer',
            'view_count'      => '0',
        ]);

        // جلب رقم المستخدم من الـ Authentication أو من الطلب أو تعيين 1 كـ fallback

        $item = KnowledgeBaseItem::create($validated);

        return response()->json([
            'message' => 'Knowledge base item created successfully',
            'data' => $item
        ], 201);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'Edit knowledge base item', 'id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        $item = KnowledgeBaseItem::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'sometimes|string|max:255',
            'summary'         => 'nullable|string',
            'content'         => 'nullable|string',
            'type'            => 'sometimes|string|max:50',
            'status'          => 'nullable|string|max:50',
            'category_id'     => 'sometimes|exists:categories,id',
            'media_url'       => 'nullable|string|max:255',
            'file_size_bytes' => 'nullable|integer',

        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Knowledge base item updated successfully',
            'data' => $item
        ]);
    }

    public function destroy(string $id)
    {
        $item = KnowledgeBaseItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Knowledge base item deleted successfully']);
    }
}