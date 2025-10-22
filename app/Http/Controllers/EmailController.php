<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        return response()->json(['message' => 'Email sent']);
    }

    public function sendSms(Request $request)
    {
        return response()->json(['message' => 'SMS sent']);
    }
}
