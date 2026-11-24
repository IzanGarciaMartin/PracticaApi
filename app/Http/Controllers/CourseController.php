<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Mostrar todos los cursos.
     */
    public function index()
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::with('academy')->get();

        return response()->json($courses, 200);
    }

    /**
     * Crear un curso dentro de una academia.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academy_id' => 'required|integer|exists:academies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'capacity' => 'required|integer|min:1',
        ]);

        $academy = Academy::findOrFail($validated['academy_id']);

        $this->authorize('create', [Course::class, $academy]);

        // El academy_id se asigna mediante la relación.
        unset($validated['academy_id']);

        $course = $academy->courses()->create($validated);

        return response()->json([
            'message' => 'Course created successfully.',
            'course' => $course,
        ], 201);
    }

    /**
     * Mostrar un curso concreto.
     */
    public function show(Course $course)
    {
        $this->authorize('view', $course);

        $course->load('academy');

        return response()->json($course, 200);
    }

    /**
     * Actualizar un curso.
     */
    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        // Comprobamos que las fechas sigan siendo coherentes,
        // incluso cuando solo se modifica una de ellas.
        $startDate = $validated['start_date'] ?? $course->start_date;
        $endDate = $validated['end_date'] ?? $course->end_date;

        if (strtotime($endDate) <= strtotime($startDate)) {
            return response()->json([
                'message' => 'The end date must be after the start date.',
                'errors' => [
                    'end_date' => [
                        'The end date must be after the start date.',
                    ],
                ],
            ], 422);
        }

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully.',
            'course' => $course,
        ], 200);
    }

    /**
     * Eliminar un curso.
     */
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        // Evitamos eliminar matrículas accidentalmente.
        if ($course->students()->exists()) {
            return response()->json([
                'message' => 'This course cannot be deleted because it has enrolled students.',
            ], 409);
        }

        $course->delete();

        return response()->noContent();
    }

    /**
     * Mostrar los cursos de una academia (endpoint público).
     */
    public function academyCourses(Academy $academy)
    {
        $courses = $academy->courses()->get();

        return response()->json($courses, 200);
    }
}
