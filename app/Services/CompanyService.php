<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService extends Service
{
    public function __construct(Company $company)
    {
        parent::__construct($company);
    }

    public function listAll(): LengthAwarePaginator
    {
        return $this->model->orderBy('name')->paginate(15);
    }

    public function save(array $data, ?int $id = null): Company
    {
        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();
    }
}
