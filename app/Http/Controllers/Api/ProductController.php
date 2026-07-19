<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(Product::orderBy('active_principle')->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'                      => 'nullable|exists:products,id',
            'active_principle'        => 'required|string',
            'concentration'           => 'required|string',
            'amount'                  => 'required|integer',
            'pharmaceutical_form'     => 'required|string',
            'commercial_presentation' => 'required|string',
            'medication_unit'         => 'required|string',
            'batch'                   => 'required|string',
            'health_register_invima'  => 'required|string',
            'expiration_date'         => 'required|date',
            'date_of_admission'       => 'required|date',
        ]);

        $expiry = Carbon::parse($data['expiration_date']);
        $monthsLeft = now()->diffInMonths($expiry, false);

        $data['semaphore'] = match (true) {
            $monthsLeft >= 12 => 'verde',
            $monthsLeft >= 3  => 'amarillo',
            default           => 'rojo',
        };

        $product = Product::updateOrCreate(['id' => $request->id], $data);

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Producto eliminado.']);
    }
}
