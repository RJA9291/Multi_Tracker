<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function show(Request $request)
    {
        $state = State::where('user_id', $request->user()->id)->first();

        return response()->json([
            'data' => $state ? $state->data : null,
            'updated_at' => $state ? $state->updated_at->toIso8601String() : null,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|array',
        ]);

        $state = State::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['data' => $validated['data']],
        );

        return response()->json([
            'ok' => true,
            'updated_at' => $state->updated_at->toIso8601String(),
        ]);
    }
}
