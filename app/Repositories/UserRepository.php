<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function create(array $attributes)
    {
        $attributes['password'] = password_hash($attributes['password'], PASSWORD_DEFAULT);
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
}