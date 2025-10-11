<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Obtener todas las tareas del usuario
     */
    public function index(): JsonResponse
    {
        $tasks = Auth::user()->tasks()
            ->with('course')
            ->orderBy('deadline', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * Crear una nueva tarea
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:assignment,exam,reading,project',
            'priority' => 'required|in:low,medium,high',
            'duration' => 'required|integer|min:15|max:480', // máximo 8 horas
            'deadline' => 'required|date|after:now',
            'energy' => 'required|in:low,medium,high',
            'description' => 'nullable|string'
        ]);

        // Verificar que el curso pertenezca al usuario
        $course = Course::where('id', $validated['course_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'El curso no existe o no pertenece al usuario'
            ], 403);
        }

        try {
            $task = Auth::user()->tasks()->create($validated);

            // Cargar la relación del curso para la respuesta
            $task->load('course');

            return response()->json([
                'success' => true,
                'message' => 'Tarea creada exitosamente',
                'data' => $task
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una tarea específica
     */
    public function show(Task $task): JsonResponse
    {
        // Verificar que la tarea pertenezca al usuario
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $task->load('course')
        ]);
    }

    /**
     * Actualizar una tarea
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        // Verificar que la tarea pertenezca al usuario
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:500',
            'course_id' => 'sometimes|exists:courses,id',
            'type' => 'sometimes|in:assignment,exam,reading,project',
            'priority' => 'sometimes|in:low,medium,high',
            'duration' => 'sometimes|integer|min:15|max:480',
            'deadline' => 'sometimes|date|after:now',
            'status' => 'sometimes|in:todo,in-progress,completed',
            'energy' => 'sometimes|in:low,medium,high',
            'description' => 'nullable|string'
        ]);

        // Si se actualiza el curso, verificar que pertenezca al usuario
        if (isset($validated['course_id'])) {
            $course = Course::where('id', $validated['course_id'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'El curso no existe o no pertenece al usuario'
                ], 403);
            }
        }

        try {
            $task->update($validated);
            $task->load('course');

            return response()->json([
                'success' => true,
                'message' => 'Tarea actualizada exitosamente',
                'data' => $task
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una tarea
     */
    public function destroy(Task $task): JsonResponse
    {
        // Verificar que la tarea pertenezca al usuario
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        try {
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tarea eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tareas por estado
     */
    public function getByStatus($status): JsonResponse
    {
        $validStatuses = ['todo', 'in-progress', 'completed'];

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Estado no válido'
            ], 400);
        }

        $tasks = Auth::user()->tasks()
            ->with('course')
            ->where('status', $status)
            ->orderBy('deadline', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }
}
