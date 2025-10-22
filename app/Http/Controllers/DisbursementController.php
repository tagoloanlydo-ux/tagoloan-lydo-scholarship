<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disburse;

class DisbursementController extends Controller
{
    public function index(Request $request)
    {
        $disbursements = Disburse::with(['scholar', 'lydopers'])->paginate(15);
        return response()->json($disbursements);
    }

    public function store(Request $request)
    {
        $disbursement = Disburse::create($request->all());
        return response()->json($disbursement, 201);
    }

    public function show($id)
    {
        $disbursement = Disburse::with(['scholar', 'lydopers'])->find($id);
        return response()->json($disbursement);
    }

    public function update(Request $request, $id)
    {
        $disbursement = Disburse::find($id);
        $disbursement->update($request->all());
        return response()->json($disbursement);
    }

    public function destroy($id)
    {
        Disburse::destroy($id);
        return response()->json(['message' => 'Disbursement deleted']);
    }

    public function pendingCount()
    {
        $count = Disburse::where('disbursement_status', 'Pending')->count();
        return response()->json(['count' => $count]);
    }
}
