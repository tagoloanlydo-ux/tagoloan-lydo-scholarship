<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lydopers;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $user = Lydopers::create($request->all());
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Lydopers::find($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id)
    {
        Lydopers::destroy($id);
        return response()->json(['message' => 'User deleted']);
    }
}
