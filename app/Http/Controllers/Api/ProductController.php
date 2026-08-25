<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
    ) {}

    public function index()
    {
        $filters = request()->only(['active_principle', 'semaphore']);

        return ProductResource::collection($this->productService->listAll($filters));
    }

    public function activePrinciples()
    {
        return response()->json($this->productService->activePrinciples());
    }

    public function store(StoreProductRequest $request)
    {
        return new ProductResource(
            $this->productService->save($request->validated(), $request->id)
        );
    }

    public function destroy(int $id)
    {
        $this->productService->delete($id);

        return response()->json(['message' => 'Producto eliminado.']);
    }
}
