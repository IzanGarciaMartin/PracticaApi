<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        $user = $request->user();

        $query = Student::query();

        // Si no es administrador, filtramos los estudiantes visibles.
        if (!$user->isAdmin()) {
            $query->where(function ($query) use ($user) {

                // Estudiantes creados por el usuario.
                $query->where('user_id', $user->id)

                    // O estudiantes matriculados en cursos
                    // de academias que pertenecen al usuario.
                    ->orWhereHas('courses.academy', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            });
        }

        $students = $query->get();

        return response()->json($students, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Student::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before_or_equal:today',
        ]);

        // El estudiante se asigna automáticamente
        // al usuario que está autenticado.
        $student = $request->user()->students()->create($validated);

        return response()->json([
            'message' => 'Student created successfully.',
            'student' => $student,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $this->authorize('view', $student);

        return response()->json($student, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'surname' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'phone' => 'sometimes|nullable|string|max:20',
            'date_of_birth' => 'sometimes|required|date|before_or_equal:today',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully.',
            'student' => $student,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        // No permitimos eliminar estudiantes matriculados.
        if ($student->courses()->exists()) {
            return response()->json([
                'message' => 'This student cannot be deleted because they are enrolled in courses.',
            ], 409);
        }

        $student->delete();

        return response()->noContent();
    }
}
