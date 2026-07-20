<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\ChangeBranchStateRequest;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Resources\BranchResource;
use App\Services\BranchService;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService,
    ) {}

    public function index()
    {
        return BranchResource::collection($this->branchService->listAll());
    }

    public function store(StoreBranchRequest $request)
    {
        return new BranchResource(
            $this->branchService->save($request->validated(), $request->id)
        );
    }

    public function show(int $id)
    {
        return new BranchResource($this->branchService->find($id));
    }

    public function changeState(ChangeBranchStateRequest $request)
    {
        return new BranchResource(
            $this->branchService->changeState($request->id, $request->state)
        );
    }

    public function select()
    {
        return $this->branchService->select();
    }
}
