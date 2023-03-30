<?php

namespace App\Services;

use App\Repositories\ProjectRepository;

class ProjectService
{
    protected $projectRepository;

    public function __construct(ProjectRepository $projectRepository) {
        $this->projectRepository = $projectRepository;
    }

    public function createProject($credentials)
    {
        $project = $this->projectRepository->create($credentials);

        if(!$project){
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating project',
            ], 401);
        }

        return response()->json([
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status,
                'slug' => $project->slug,
                'tasks_count' => 0,
                'completed_tasks_count' => 0,
            ]
        ], 201);
    }

    public function getAllProjects($fields)
    {
        $order_by = "title";
        $order_type = "asc";

        switch ($fields['sortBy']){
            case 'alpha_desc':
                $order_type = "desc";
                break;
            case 'create':
                $order_by = "created_at";
            case 'update':
                $order_by = "updated_at";
        }

        $statuses = ['opened'];

        if (isset($fields['withClosed']) && $fields['withClosed'] == 1) {
            $statuses = ['opened', 'closed'];
        } else if (isset($fields['onlyClosed']) && $fields['onlyClosed'] == 1) {
            $statuses = ['closed'];
        }

        $projects = $this->projectRepository->getOrderedProjects($order_by, $order_type, $statuses, $fields['perPage'], $fields['page']);

        if(!$projects){
            return response()->json([
                'status' => 'error',
                'message' => 'Error returning projects',
            ], 401);
        }

        return response()->json(['data' => $projects], 200);
    }

    public function getByIdOrSlug($idOrSlug)
    {
        $project = $this->projectRepository->getByIdOrSlug($idOrSlug);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error returning project',
            ], 401);
        }

        return response()->json([
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status,
                'slug' => $project->slug,
                'tasks_count' => $project->tasks()->count(),
                'completed_tasks_count' => $project->tasks()->where('status', 'closed')->count(),
            ]
        ], 200);
    }

    public function modifyProject($fields, $idOrSlug)
    {
        $project = $this->projectRepository->getByIdOrSlug($idOrSlug);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding project',
            ], 401);
        }

        $project = $this->projectRepository->update($project->id, $fields);

        return response()->json([
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status,
                'slug' => $project->slug,
                'tasks_count' => $project->tasks()->count(),
                'completed_tasks_count' => $project->tasks()->where('status', 'closed')->count(),
            ]
        ], 200);
    }

    public function modifyProjectStatus($idOrSlug, $status)
    {
        $project = $this->projectRepository->getByIdOrSlug($idOrSlug);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding project',
            ], 401);
        }

        $status_to_pass = 'opened';
        if($status == 'close'){
            $status_to_pass = 'closed';
        }

        if($project->status == 'closed' && $status_to_pass == 'opened'){
            // ritorno un errore, un progetto chiuso non può riaprirsi
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request',
            ], 400);
        }

        if($status_to_pass == 'closed' && $project->tasks()->where('status', '!=', 'closed')->count() > 0){
           // ritorno un errore, un progetto con task aperti non può essere chiuso
           return response()->json([
                'status' => 'error',
                'message' => 'Bad request',
            ], 400);
        }
        
        $fields['status'] = $status_to_pass;
        $project = $this->projectRepository->update($project->id, $fields);

        return response(null, 204);
    }
}