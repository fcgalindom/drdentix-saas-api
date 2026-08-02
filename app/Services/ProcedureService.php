<?php

namespace App\Services;

use App\Models\Procedure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProcedureService extends Service
{
    public function __construct(Procedure $procedure)
    {
        parent::__construct($procedure);
    }

    public function listAll(): LengthAwarePaginator
    {
        return $this->model->where('company_id', $this->companyId())->orderBy('name')->paginate(15);
    }

    public function save(array $data, ?int $id = null): Procedure
    {
        $data['company_id'] = $this->companyId();

        return DB::transaction(fn () => $this->model->updateOrCreate(['id' => $id], $data));
    }

    public function changeState(int $id, string $state): Procedure
    {
        $procedure = $this->model->where('company_id', $this->companyId())->findOrFail($id);
        $procedure->update(['state' => $state]);

        return $procedure;
    }

    public function select(): Collection
    {
        return $this->model
            ->where('company_id', $this->companyId())
            ->where('state', 'Activo')
            ->orderBy('name')
            ->get(['id', 'name', 'duration']);
    }
}
