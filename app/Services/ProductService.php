<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService extends Service
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function listAll(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('company_id', $this->companyId());

        if (! empty($filters['active_principle'])) {
            $query->where('active_principle', 'like', '%'.$filters['active_principle'].'%');
        }

        if (! empty($filters['semaphore'])) {
            $query->where('semaphore', $filters['semaphore']);
        }

        return $query->orderBy('active_principle')->paginate(15);
    }

    public function activePrinciples(): Collection
    {
        return $this->model
            ->where('company_id', $this->companyId())
            ->distinct()
            ->orderBy('active_principle')
            ->pluck('active_principle');
    }

    public function save(array $data, ?int $id = null): Product
    {
        $data['company_id'] = $this->companyId();
        $expiry = Carbon::parse($data['expiration_date']);
        $monthsLeft = now()->diffInMonths($expiry, false);

        $data['semaphore'] = match (true) {
            $monthsLeft >= 12 => 'verde',
            $monthsLeft >= 3 => 'amarillo',
            default => 'rojo',
        };

        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function delete(int $id): void
    {
        $this->model->where('company_id', $this->companyId())->findOrFail($id)->delete();
    }
}
