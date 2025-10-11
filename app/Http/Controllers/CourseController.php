<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function getUserCourses(): JsonResponse
    {
        try {
            $courses = Auth::user()->courses()
                ->select('id', 'name', 'color', 'credits') // Solo campos necesarios
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $courses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los cursos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $courses = Auth::user()->courses()->get();

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'credits' => 'required|integer|min:1|max:10'
        ]);

        try {
            $course = Auth::user()->courses()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Curso creado exitosamente',
                'data' => $course
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el curso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): JsonResponse
    {
        if ($course->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $course->load('tasks')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): JsonResponse
    {
        if ($course->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:50',
            'credits' => 'sometimes|integer|min:1|max:10'
        ]);

        try {
            $course->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Curso actualizado exitosamente',
                'data' => $course
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el curso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): JsonResponse
    {
        if ($course->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        try {
            $course->delete();

            return response()->json([
                'success' => true,
                'message' => 'Curso eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el curso',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
