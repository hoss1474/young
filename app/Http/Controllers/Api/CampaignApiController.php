<?php

namespace App\Http\Controllers\Api;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class CampaignApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = Campaign::all();
        return response()->json([
            'data' => $campaigns,
            'message' => 'Campaigns retrieved successfully',
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:55',
            'description' => 'nullable|max:65535',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'required|numeric',
        ]);

        $campaign = Campaign::create($validated);

        return response()->json([
            'data' => $campaign,
            'message' => 'Campaign created successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        return response()->json([
            'data' => $campaign,
            'message' => 'Campaign retrieved successfully',
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|max:55',
            'description' => 'nullable|max:65535',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'required|numeric',
        ]);

        $campaign->update($validated);

        return response()->json([
            'data' => $campaign,
            'message' => 'Campaign updated successfully',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return response()->json([
            'message' => 'Campaign deleted successfully',
        ], Response::HTTP_NO_CONTENT);
    }
}
