<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Resources\PlanResource;
use App\Services\PlanService;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $planService,
    ) {}

    public function index()
    {
        return PlanResource::collection($this->planService->listAll());
    }

    public function publicIndex()
    {
        return PlanResource::collection($this->planService->publicList());
    }

    public function store(StorePlanRequest $request)
    {
        return new PlanResource(
            $this->planService->save($request->validated(), $request->id)
        );
    }

    public function destroy(int $id)
    {
        $this->planService->delete($id);

        return response()->json(['message' => 'Plan eliminado.']);
    }
}
