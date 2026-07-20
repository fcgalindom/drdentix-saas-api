<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService extends Service
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function listAll(): LengthAwarePaginator
    {
        return $this->model->orderBy('active_principle')->paginate(15);
    }

    public function save(array $data, ?int $id = null): Product
    {
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
        $this->find($id)->delete();
    }
}
