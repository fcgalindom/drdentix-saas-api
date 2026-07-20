<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dentist\ChangeDentistStateRequest;
use App\Http\Requests\Dentist\StoreDentistRequest;
use App\Http\Requests\Dentist\StoreScheduleRequest;
use App\Http\Resources\DentistResource;
use App\Http\Resources\ScheduleResource;
use App\Services\DentistService;
use Illuminate\Http\Request;

class DentistController extends Controller
{
    public function __construct(
        protected DentistService $dentistService,
    ) {}

    public function index(Request $request)
    {
        return DentistResource::collection(
            $this->dentistService->listAll($request->only('name', 'city', 'document'))
        );
    }

    public function store(StoreDentistRequest $request)
    {
        return new DentistResource(
            $this->dentistService->save($request->validated(), $request->id)
        );
    }

    public function show(int $id)
    {
        return new DentistResource($this->dentistService->findById($id));
    }

    public function changeState(ChangeDentistStateRequest $request)
    {
        return new DentistResource(
            $this->dentistService->changeState($request->id, $request->state)
        );
    }

    public function select()
    {
        return $this->dentistService->select();
    }

    public function getSchedule(Request $request)
    {
        return ScheduleResource::collection(
            $this->dentistService->getSchedule($request->dentist_id, $request->user())
        );
    }

    public function storeSchedule(StoreScheduleRequest $request)
    {
        return ScheduleResource::collection(
            $this->dentistService->saveSchedule($request->validated())
        );
    }

    public function myAppointments(Request $request)
    {
        $appointments = $this->dentistService->myAppointments(
            $request->user(),
            $request->date,
        );

        return response()->json($appointments);
    }
}
