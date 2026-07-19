<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        return PromotionResource::collection(
            Promotion::orderByDesc('date_start')->paginate(15)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'             => 'nullable|exists:promotions,id',
            'date_start'     => 'required|date',
            'date_end'       => 'required|date|after_or_equal:date_start',
            'details'        => 'required|string|max:600',
            'discount'       => 'required|integer',
            'limit_patients' => 'required|integer|max:300',
        ]);

        $promotion = Promotion::updateOrCreate(['id' => $request->id], $data);

        return new PromotionResource($promotion);
    }

    public function deactivate(Request $request)
    {
        $request->validate(['id' => 'required|exists:promotions,id']);

        Promotion::findOrFail($request->id)->update(['status' => 0]);

        return response()->json(['message' => 'Promoción desactivada.']);
    }
}
