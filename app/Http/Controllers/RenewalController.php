<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Renewal;

class RenewalController extends Controller
{
    public function index(Request $request)
    {
        $renewals = Renewal::with(['scholar'])->paginate(15);
        return response()->json($renewals);
    }

    public function store(Request $request)
    {
        $renewal = Renewal::create($request->all());
        return response()->json($renewal, 201);
    }

    public function show($id)
    {
        $renewal = Renewal::with(['scholar'])->find($id);
        return response()->json($renewal);
    }

    public function update(Request $request, $id)
    {
        $renewal = Renewal::find($id);
        $renewal->update($request->all());
        return response()->json($renewal);
    }

    public function destroy($id)
    {
        Renewal::destroy($id);
        return response()->json(['message' => 'Renewal deleted']);
    }

    public function pendingCount()
    {
        $count = Renewal::where('renewal_status', 'Pending')->count();
        return response()->json(['count' => $count]);
    }

    public function getRequirements($id)
    {
        $renewal = Renewal::find($id);
        return response()->json([
            'renewal_cert_of_reg' => $renewal->renewal_cert_of_reg,
            'renewal_grade_slip' => $renewal->renewal_grade_slip,
            'renewal_brgy_indigency' => $renewal->renewal_brgy_indigency,
        ]);
    }
}
