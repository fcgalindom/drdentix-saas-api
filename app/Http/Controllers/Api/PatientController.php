<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with('user')
            ->withCount(['appointments as paid_count' => fn($q) => $q->where('state', 'Pagado')]);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('document')) {
            $query->whereHas('user', fn($q) => $q->where('document', 'like', '%' . $request->document . '%'));
        }

        return PatientResource::collection($query->orderByDesc('paid_count')->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'        => 'nullable|exists:patients,id',
            'name'      => 'required|string|max:70',
            'city'      => 'nullable|string|max:70',
            'telephone' => 'required|string|max:70',
            'document'  => 'required|string|max:20',
            'email'     => 'nullable|email|max:70',
            'birth'     => 'nullable|date|before:today',
        ]);

        return DB::transaction(function () use ($request, $data) {
            if ($request->filled('id')) {
                $patient = Patient::findOrFail($request->id);
                $patient->update([
                    'name'      => $data['name'],
                    'city'      => $data['city'] ?? null,
                    'telephone' => $data['telephone'],
                ]);
                $patient->user->update(array_filter([
                    'document' => $data['document'],
                    'email'    => $data['email'] ?? null,
                    'birth'    => $data['birth'] ?? null,
                ]));
            } else {
                $user = User::create([
                    'document'  => $data['document'],
                    'email'     => $data['email'] ?? null,
                    'password'  => Hash::make('1234'),
                    'type_user' => 'Patient',
                    'state'     => 'Activo',
                    'birth'     => $data['birth'] ?? null,
                ]);
                $patient = Patient::create([
                    'name'      => $data['name'],
                    'city'      => $data['city'] ?? null,
                    'telephone' => '+57' . ltrim($data['telephone'], '+57'),
                    'id_user'   => $user->id,
                ]);
            }

            return new PatientResource($patient->load('user'));
        });
    }

    public function show(Patient $patient)
    {
        return new PatientResource($patient->load('user'));
    }

    public function me(Request $request)
    {
        $patient = Patient::with('user')
            ->where('id_user', $request->user()->id)
            ->firstOrFail();

        return new PatientResource($patient);
    }

    public function deactivate(Request $request)
    {
        $request->validate(['id' => 'required|exists:patients,id', 'state' => 'required|string']);

        $patient = Patient::with('user')->findOrFail($request->id);
        $patient->user->update([
            'state'    => $request->state,
            'email'    => time() . '@gmail.com',
            'document' => (string) time(),
        ]);

        return response()->json(['message' => 'Paciente desactivado.']);
    }

    // Select list for dropdowns (document - name)
    public function select()
    {
        return Patient::with('user')
            ->whereHas('user', fn($q) => $q->where('state', 'Activo'))
            ->get()
            ->map(fn($p) => ['id' => $p->id, 'text' => $p->user->document . ' - ' . $p->name]);
    }

    public function findByDocument(Request $request)
    {
        $request->validate(['document' => 'required|string']);

        $user = User::where('document', $request->document)->where('type_user', 'Patient')->first();

        if (!$user) {
            return response()->json(['status' => 422, 'document' => $request->document]);
        }

        return response()->json(['status' => 200, 'id' => $user->patient->id]);
    }
}
