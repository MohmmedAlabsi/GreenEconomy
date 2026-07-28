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
    public function show(KnowledgeBaseItem $knowledgeBase)
    {
        // زيادة عدد المشاهدات عند القراءة
        $knowledgeBase->increment('view_count');
        return response()->json($knowledgeBase->load(['category', 'user']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
