<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseItem;
use App\Http\Requests\StoreKnowledgeBaseRequest;
use App\Http\Requests\UpdateKnowledgeBaseRequest;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $items = KnowledgeBaseItem::with(['category', 'user'])->latest()->get();
        return response()->json(['success' => true, 'data' => $items]);
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

    public function store(StoreKnowledgeBaseRequest $request)
    {
        $item = KnowledgeBaseItem::create($request->validated());

        return response()->json([
            'message' => 'Knowledge base item created successfully',
            'data' => $item
        ], 201);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'Edit knowledge base item', 'id' => $id]);
    }

    public function update(UpdateKnowledgeBaseRequest $request, string $id)
    {
        $item = KnowledgeBaseItem::findOrFail($id);
        $item->update($request->validated());

        return response()->json([
            'message' => 'Knowledge base item updated successfully',
            'data' => $item
        ]);
    }

    public function destroy(string $id)
    {
        KnowledgeBaseItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Knowledge base item deleted successfully']);
    }
}