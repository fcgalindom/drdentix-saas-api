<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Services\CompanyService;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $companyService,
    ) {}

    public function index()
    {
        return CompanyResource::collection($this->companyService->listAll());
    }

    public function store(StoreCompanyRequest $request)
    {
        return new CompanyResource(
            $this->companyService->save($request->validated(), $request->id)
        );
    }

    public function destroy(int $id)
    {
        $this->companyService->delete($id);

        return response()->json(['message' => 'Empresa eliminada.']);
    }
}
