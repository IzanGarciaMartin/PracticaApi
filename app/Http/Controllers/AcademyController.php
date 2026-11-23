<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use Illuminate\Http\Request;

class AcademyController extends Controller
{
    /**
     * Mostrar todas las academias.
     */
    public function index()
    {
        $this->authorize('viewAny', Academy::class);

        $academies = Academy::all();

        return response()->json($academies, 200);
    }

    /**
     * Crear una academia.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Academy::class);

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
     * Mostrar una academia concreta.
     */
    public function show(Academy $academy)
    {
        $this->authorize('view', $academy);

        return response()->json($academy, 200);
    }

    /**
     * Actualizar una academia.
     */
    public function update(Request $request, Academy $academy)
    {
        $this->authorize('update', $academy);

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
     * Eliminar una academia.
     */
    public function destroy(Academy $academy)
    {
        $this->authorize('delete', $academy);

        if ($academy->courses()->exists()) {
            return response()->json([
                'message' => 'This academy cannot be deleted because it has associated courses.',
            ], 409);
        }

        $academy->delete();

        return response()->noContent();
    }
}
