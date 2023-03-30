<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository implements TaskProjectRepositoryInterface
{
    protected $model;

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getByIdOrSlug($idOrSlug)
    {
        return $this->model::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->first();
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $model = $this->getById($id);
        $model->update($attributes);
        return $model;
    }

    public function delete($id)
    {
        $model = $this->getById($id);
        $model->delete();
    }

    public function getOrderedProjects($order_by, $order_type, $statuses, $perPage, $page)
    {
        return $this->model::whereIn('status', $statuses)
            ->withCount(['tasks' => function ($query) {
            }])
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'closed');
            }])
            ->orderBy($order_by, $order_type)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
    }
}