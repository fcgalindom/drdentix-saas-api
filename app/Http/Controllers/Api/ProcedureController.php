<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProcedureResource;
use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcedureController extends Controller
{
    public function index()
    {
        return ProcedureResource::collection(
            Procedure::orderBy('name')->paginate(15)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'       => 'nullable|exists:procedures,id',
            'name'     => 'required|string|max:70',
            'duration' => 'required|integer|min:1',
            'state'    => 'sometimes|string',
        ]);

        $procedure = DB::transaction(fn() => Procedure::updateOrCreate(['id' => $request->id], $data));

        return new ProcedureResource($procedure);
    }

    public function changeState(Request $request)
    {
        $request->validate(['id' => 'required|exists:procedures,id', 'state' => 'required|string']);

        $procedure = Procedure::findOrFail($request->id);
        $procedure->update(['state' => $request->state]);

        return new ProcedureResource($procedure);
    }

    // Select list for dropdowns
    public function select()
    {
        return Procedure::where('state', 'Activo')
            ->orderBy('name')
            ->get(['id', 'name', 'duration']);
    }
}
