<?php

namespace App\Repositories;

interface TaskProjectRepositoryInterface
{
    public function getById($id);

    public function getByIdOrSlug($id);

    public function getAll();

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);

}