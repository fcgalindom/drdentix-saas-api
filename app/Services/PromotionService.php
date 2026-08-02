<?php

namespace App\Services;

use App\Models\Promotion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PromotionService extends Service
{
    public function __construct(Promotion $promotion)
    {
        parent::__construct($promotion);
    }

    public function listAll(): LengthAwarePaginator
    {
        return $this->model->where('company_id', $this->companyId())->orderByDesc('date_start')->paginate(15);
    }

    public function save(array $data, ?int $id = null): Promotion
    {
        $data['company_id'] = $this->companyId();

        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function deactivate(int $id): void
    {
        $this->model->where('company_id', $this->companyId())->findOrFail($id)->update(['status' => 0]);
    }
}
