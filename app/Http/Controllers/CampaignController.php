<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function create()
    {
        return view('campaign.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:55',
        ]);

        Campaign::create($validated);

        return redirect()->back()->with('success', 'کمپین با موفقیت اضافه شد!');
    }
}
