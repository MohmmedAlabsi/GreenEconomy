<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use Illuminate\Routing\Controller;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // عرض كل المناطق الجغرافية
    public function index()
    {
        $regions = Region::all();
        return response()->json($regions);
    }

    // عرض منطقة معينة مع دراسات الجدوى المرتبطة بها
    public function show(Region $region)
    {
        return response()->json($region->load(['feasibilityStudies', 'feasibilityRequests']));
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
     * Display the specified resource.
     */

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
