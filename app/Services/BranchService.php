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

    public function listAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('company_id', $this->companyId());

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        return $query->orderBy('name')->paginate(15);
    }

    public function save(array $data, ?int $id = null): Branch
    {
        $data['company_id'] = $this->companyId();

        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function changeState(int $id, string $state): Branch
    {
        $branch = $this->model->where('company_id', $this->companyId())->findOrFail($id);
        $branch->update(['state' => $state]);

        return $branch;
    }

    public function select(): Collection
    {
        return $this->model
            ->where('company_id', $this->companyId())
            ->where('state', 'Activo')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function cities(): Collection
    {
        return $this->model
            ->where('company_id', $this->companyId())
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->get(['city']);
    }
}
