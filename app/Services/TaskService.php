<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;

class TaskService
{
    protected $taskRepository;
    protected $projectRepository;
    protected $userRepository;

    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository, UserRepository $userRepository) {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function createTask($fields, $idOrSlug)
    {
        // Devo controllare se esiste il progetto
        $project = $this->projectRepository->getByIdOrSlug($idOrSlug);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding project',
            ], 401);
        }

        if ($project->status == 'closed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating task for closed project',
            ], 401);
        }

        $fields['project_id'] = $project->id;

        // Devo controllare se esiste l'utente
        $user = $this->userRepository->getById($fields['user_id']);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding user',
            ], 401);
        }

        $task = $this->taskRepository->create($fields);

        if(!$task){
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating task',
            ], 401);
        }

        return response()->json([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'assignee' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                ],
                'slug' => $task->slug,
                'difficulty' => $task->difficulty,
                'priority' => $task->priority,
                'status' => $task->status,
            ]
        ], 201);
    }

    public function getAllTasks($fields, $idOrSlug)
    {
        // Devo controllare se esiste il progetto
        $project = $this->projectRepository->getByIdOrSlug($idOrSlug);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding project',
            ], 401);
        }

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

        $tasks = $this->taskRepository->getOrderedTasks($order_by, $order_type, $statuses, $fields['perPage'], $fields['page'], $project->id);

        if(!$tasks){
            return response()->json([
                'status' => 'error',
                'message' => 'Error returning tasks',
            ], 401);
        }

        $task_data = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'slug' => $task->slug,
                'assignee' => [
                    'id' => $task->user->id,
                    'name' => $task->user->name,
                    'username' => $task->user->username,
                ],
                'difficulty' => $task->difficulty,
                'priority' => $task->priority,
                'status' => $task->status,
            ];
        });
        
        return response()->json($task_data, 200);
    }

    public function getByIdOrSlug($idOrSlugProject, $idOrSlugTask)
    {
        $project = $this->projectRepository->getByIdOrSlug($idOrSlugProject);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error returning project',
            ], 401);
        }

        $task = $this->taskRepository->getByIdOrSlug($idOrSlugTask);

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error returning task',
            ], 401);
        }

        return response()->json([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'slug' => $task->slug,
                'assignee' => [
                    'id' => $task->user->id,
                    'name' => $task->user->name,
                    'username' => $task->user->username,
                ],
                'difficulty' => $task->difficulty,
                'priority' => $task->priority,
                'status' => $task->status,
            ]
        ], 200);
    }

    public function modifyTask($fields, $idOrSlugProject, $idOrSlugTask)
    {
        $project = $this->projectRepository->getByIdOrSlug($idOrSlugProject);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding project',
            ], 401);
        }

        $task = $this->taskRepository->getByIdOrSlug($idOrSlugTask);

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error returning task',
            ], 401);
        }

        // Devo controllare se esiste l'utente
        $user = $this->userRepository->getById($fields['user_id']);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding user',
            ], 401);
        }

        $task = $this->taskRepository->update($task->id, $fields);

        return response()->json([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'slug' => $task->slug,
                'assignee' => [
                    'id' => $task->user->id,
                    'name' => $task->user->name,
                    'username' => $task->user->username,
                ],
                'difficulty' => $task->difficulty,
                'priority' => $task->priority,
                'status' => $task->status,
            ]
        ], 200);
    }

    public function modifyTaskStatus($idOrSlugProject, $idOrSlugTask, $status)
    {
        $project = $this->projectRepository->getByIdOrSlug($idOrSlugProject);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding project',
            ], 401);
        }

        if($project->status == 'closed'){
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request',
            ], 400);
        }

        $task = $this->taskRepository->getByIdOrSlug($idOrSlugTask);

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error returning task',
            ], 401);
        }

        $status_to_pass = 'opened';
        if($status == 'close'){
            $status_to_pass = 'closed';
        }
        if($status == 'block'){
            $status_to_pass = 'blocked';
        }
        
        $fields['status'] = $status_to_pass;
        $task = $this->taskRepository->update($task->id, $fields);

        return response(null, 204);
    }
}