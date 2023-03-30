<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

use App\Services\ProjectService;
use App\Models\Project;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function createProject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'in:opened,closed,blocked'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $fields = $request->only('title', 'description', 'status');

        return $this->projectService->createProject($fields);
    }

    public function getAllProjects(Request $request): JsonResponse
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

        return $this->projectService->getAllProjects($fields);
    }

    public function getProject(string $project): JsonResponse
    {
        $validator = Validator::make(['project' => $project], [
            'project' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        return $this->projectService->getByIdOrSlug($project);
    }

    public function modifyProject(Request $request, string $project): JsonResponse
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
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $fields = $request->only('title', 'description');

        return $this->projectService->modifyProject($fields, $project);
    }

    public function modifyProjectStatus(string $project, string $status)
    {
        $validator = Validator::make(['project' => $project, 'status' => $status], [
            'project' => 'required|string|max:255',
            'status' => 'required|in:open,close',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        return $this->projectService->modifyProjectStatus($project, $status);
    }
}
