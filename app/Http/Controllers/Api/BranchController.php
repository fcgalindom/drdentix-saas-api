<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('name')->paginate(15);

        return BranchResource::collection($branches);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:70',
            'address' => 'required|string|max:70',
            'contact' => 'required|string|max:70',
            'city'    => 'required|string|max:70',
            'state'   => 'sometimes|string',
        ]);

        $branch = Branch::updateOrCreate(['id' => $request->id], $data);

        return new BranchResource($branch);
    }

    public function show(Branch $branch)
    {
        return new BranchResource($branch);
    }

    public function changeState(Request $request)
    {
        $request->validate(['id' => 'required|exists:branches,id', 'state' => 'required|string']);

        $branch = Branch::findOrFail($request->id);
        $branch->update(['state' => $request->state]);

        return new BranchResource($branch);
    }

    // Select list for dropdowns
    public function select()
    {
        return Branch::where('state', 'Activo')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
