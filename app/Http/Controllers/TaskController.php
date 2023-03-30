<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function createTask(Request $request, string $project): JsonResponse
    {
        $validator = Validator::make(['project' => $project], [
            'project' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|string',
            'difficulty' => 'required|integer',
            'priority' => 'in:low,medium,high,very high'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $fields = $request->only('title', 'description', 'user_id', 'difficulty', 'priority');

        return $this->taskService->createTask($fields, $project);
    }

    public function getAllTasks(Request $request, string $project): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer',
            'perPage' => 'required|integer',
            'sortBy' => 'required|in:alpha_desc,alpha_asc,create,update',
            'withClosed' => 'nullable|boolean',
            'onlyClosed' => 'nullable|boolean',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $fields = $request->only('page', 'perPage', 'sortBy', 'withClosed', 'onlyClosed');

        return $this->taskService->getAllTasks($fields, $project);
    }

    public function getTask(string $project, string $task): JsonResponse
    {
        $validator = Validator::make(['project' => $project, 'task' => $task], [
            'project' => 'required|string|max:255',
            'task' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        return $this->taskService->getByIdOrSlug($project, $task);
    }

    public function modifyTask(Request $request, string $project, string $task): JsonResponse
    {
        $validator = Validator::make(['project' => $project, 'task' => $task], [
            'project' => 'required|string|max:255',
            'task' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|string',
            'difficulty' => 'required|integer',
            'priority' => 'in:low,medium,high,very high'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $fields = $request->only('title', 'description', 'user_id', 'difficulty', 'priority');

        return $this->taskService->modifyTask($fields, $project, $task);
    }

    public function modifyTaskStatus(string $project, string $task, string $status)
    {
        $validator = Validator::make(['project' => $project, 'status' => $status, 'task' => $task], [
            'project' => 'required|string|max:255',
            'status' => 'required|in:open,close,block',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        return $this->taskService->modifyTaskStatus($project, $task, $status);
    }
}
