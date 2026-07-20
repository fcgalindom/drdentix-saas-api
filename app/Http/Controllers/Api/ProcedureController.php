<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procedure\ChangeProcedureStateRequest;
use App\Http\Requests\Procedure\StoreProcedureRequest;
use App\Http\Resources\ProcedureResource;
use App\Services\ProcedureService;

class ProcedureController extends Controller
{
    public function __construct(
        protected ProcedureService $procedureService,
    ) {}

    public function index()
    {
        return ProcedureResource::collection($this->procedureService->listAll());
    }

    public function store(StoreProcedureRequest $request)
    {
        return new ProcedureResource(
            $this->procedureService->save($request->validated(), $request->id)
        );
    }

    public function changeState(ChangeProcedureStateRequest $request)
    {
        return new ProcedureResource(
            $this->procedureService->changeState($request->id, $request->state)
        );
    }

    public function select()
    {
        return $this->procedureService->select();
    }
}
