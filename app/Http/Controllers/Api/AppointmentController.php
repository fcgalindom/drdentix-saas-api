<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\AvailableSlotsRequest;
use App\Http\Requests\Appointment\ByDocumentRequest;
use App\Http\Requests\Appointment\CancelAppointmentRequest;
use App\Http\Requests\Appointment\ChangeAppointmentStateRequest;
use App\Http\Requests\Appointment\DeleteAppointmentRequest;
use App\Http\Requests\Appointment\DentistsByProcedureRequest;
use App\Http\Requests\Appointment\MarkPhoneRequest;
use App\Http\Requests\Appointment\MarkWhatsappRequest;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\DentistProcedureResource;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService,
    ) {}

    public function formData(Request $request)
    {
        return response()->json($this->appointmentService->formData());
    }

    public function index(Request $request)
    {
        $result = $this->appointmentService->listAll(
            $request->only('patient', 'date_from', 'date_to', 'dentist_id', 'state', 'advance')
        );

        return response()->json([
            'data' => AppointmentResource::collection($result['appointments'])->response()->getData(true),
            'income' => $result['income'],
            'pending' => $result['pending'],
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        return new AppointmentResource(
            $this->appointmentService->createAppointment($request->validated(), $request->user())
        );
    }

    public function show(int $id)
    {
        return new AppointmentResource($this->appointmentService->findById($id));
    }

    public function changeState(ChangeAppointmentStateRequest $request)
    {
        return new AppointmentResource(
            $this->appointmentService->changeState($request->id, $request->state, $request->payments)
        );
    }

    public function delete(DeleteAppointmentRequest $request)
    {
        $this->appointmentService->deleteAppointment($request->id);

        return response()->json(['message' => 'Cita eliminada.']);
    }

    public function cancel(CancelAppointmentRequest $request)
    {
        $this->appointmentService->cancelAppointment($request->id, $request->user());

        return response()->json(['message' => 'Cita cancelada.']);
    }

    public function byPatient(Request $request)
    {
        return AppointmentResource::collection(
            $this->appointmentService->byPatient($request->patient_id, $request->user())
        );
    }

    public function byDocument(ByDocumentRequest $request)
    {
        return AppointmentResource::collection(
            $this->appointmentService->byDocument($request->document)
        );
    }

    public function availableSlots(AvailableSlotsRequest $request)
    {
        $slots = $this->appointmentService->availableSlots(
            $request->dentist_procedure_id,
            $request->date,
        );

        return response()->json(['slots' => $slots]);
    }

    public function dentistsByProcedure(DentistsByProcedureRequest $request)
    {
        return DentistProcedureResource::collection(
            $this->appointmentService->dentistsByProcedure($request->procedure_id)
        );
    }

    public function markWhatsapp(MarkWhatsappRequest $request)
    {
        $result = $this->appointmentService->markWhatsapp($request->id);

        return response()->json($result);
    }

    public function markPhone(MarkPhoneRequest $request)
    {
        $this->appointmentService->markPhone($request->id);

        return response()->json(['message' => 'Llamada registrada.']);
    }
}
