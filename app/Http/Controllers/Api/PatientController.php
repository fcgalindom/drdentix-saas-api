<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\DeactivatePatientRequest;
use App\Http\Requests\Patient\FindByDocumentRequest;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Resources\PatientResource;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(
        protected PatientService $patientService,
    ) {}

    public function index(Request $request)
    {
        return PatientResource::collection(
            $this->patientService->listAll($request->only('name', 'city', 'document'))
        );
    }

    public function store(StorePatientRequest $request)
    {
        return new PatientResource(
            $this->patientService->save($request->validated(), $request->id)
        );
    }

    public function show(int $id)
    {
        return new PatientResource($this->patientService->findById($id));
    }

    public function me(Request $request)
    {
        return new PatientResource($this->patientService->me($request->user()));
    }

    public function deactivate(DeactivatePatientRequest $request)
    {
        $this->patientService->deactivate($request->id, $request->state);

        return response()->json(['message' => 'Paciente desactivado.']);
    }

    public function select()
    {
        return $this->patientService->select();
    }

    public function findByDocument(FindByDocumentRequest $request)
    {
        $result = $this->patientService->findByDocument($request->document);

        return response()->json($result);
    }
}
