<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\BillingByPatientRequest;
use App\Http\Resources\AppointmentResource;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
    ) {}

    public function staffGraph()
    {
        return response()->json($this->reportService->staffGraph());
    }

    public function billingByPatient(BillingByPatientRequest $request)
    {
        return AppointmentResource::collection(
            $this->reportService->billingByPatient($request->patient_id)
        );
    }
}
