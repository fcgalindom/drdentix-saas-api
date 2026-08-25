<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Promotion\ChangeStatePromotionRequest;
use App\Http\Requests\Promotion\DeactivatePromotionRequest;
use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Services\PromotionService;

class PromotionController extends Controller
{
    public function __construct(
        protected PromotionService $promotionService,
    ) {}

    public function index()
    {
        return PromotionResource::collection($this->promotionService->listAll());
    }

    public function store(StorePromotionRequest $request)
    {
        return new PromotionResource(
            $this->promotionService->save($request->validated(), $request->id)
        );
    }

    public function deactivate(DeactivatePromotionRequest $request)
    {
        $this->promotionService->deactivate($request->id);

        return response()->json(['message' => 'Promoción desactivada.']);
    }

    public function changeState(ChangeStatePromotionRequest $request)
    {
        $this->promotionService->changeState($request->id, $request->state);

        return response()->json(['message' => 'Estado de promoción actualizado.']);
    }
}
