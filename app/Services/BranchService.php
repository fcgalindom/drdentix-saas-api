<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BranchService extends Service
{
    public function __construct(Branch $branch)
    {
        parent::__construct($branch);
    }

    public function listAll(): LengthAwarePaginator
    {
        return $this->model->orderBy('name')->paginate(15);
    }

    public function save(array $data, ?int $id = null): Branch
    {
        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function changeState(int $id, string $state): Branch
    {
        $branch = $this->find($id);
        $branch->update(['state' => $state]);

        return $branch;
    }

    public function select(): Collection
    {
        return $this->model
            ->where('state', 'Activo')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
