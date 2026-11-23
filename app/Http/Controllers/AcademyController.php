<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use Illuminate\Http\Request;

class AcademyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academies = Academy::all();

        return response()->json($academies, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $academy = $request->user()->academies()->create($validated);

        return response()->json([
            'message' => 'Academy created successfully.',
            'academy' => $academy,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Academy $academy)
    {
        return response()->json($academy, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Academy $academy)
    {
        if (
            $academy->user_id !== $request->user()->id &&
            !$request->user()->isAdmin()
        ) {
            return response()->json([
                'message' => 'You are not authorized to modify this academy.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'address' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'email' => 'sometimes|nullable|email|max:255',
        ]);

        $academy->update($validated);

        return response()->json([
            'message' => 'Academy updated successfully.',
            'academy' => $academy,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Academy $academy)
    {
        if (
            $academy->user_id !== $request->user()->id &&
            !$request->user()->isAdmin()
        ) {
            return response()->json([
                'message' => 'You are not authorized to delete this academy.',
            ], 403);
        }

        if ($academy->courses()->exists()) {
            return response()->json([
                'message' => 'This academy cannot be deleted because it has associated courses.',
            ], 409);
        }

        $academy->delete();

        return response()->noContent();
    }
}
