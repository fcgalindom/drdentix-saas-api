<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PlanService extends Service
{
    public function __construct(Plan $plan)
    {
        parent::__construct($plan);
    }

    public function listAll(): LengthAwarePaginator
    {
        return $this->model->orderBy('name')->paginate(15);
    }

    public function publicList(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function save(array $data, ?int $id = null): Plan
    {
        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();
    }
}
