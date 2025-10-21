<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function testSend()
    {
        $send_data = [];

        $send_data['mobile'] = '+639758669139';
        $send_data['message'] = 'Testing Message! XYZ';
        $send_data['token'] = env('QPROXY_SMS_TOKEN', '8759da3d7302494a1e0d3d8f2e246b21');
        $parameters = json_encode($send_data);
        Log::info("SMS parameters: " . $parameters);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('QPROXY_SMS_URL', 'https://sms.ckent.dev/api/sms/v1/send'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [];
        $headers = array(
            "Content-Type: application/json"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $get_sms_status = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        Log::info("SMS API response: " . $get_sms_status);
        if ($curl_error) {
            Log::error("Curl error: " . $curl_error);
        }

        return response()->json(['status' => $get_sms_status]);
    }

    public function sendSms($mobile, $message)
    {
        Log::info("SmsController sendSms called with mobile: " . $mobile . " message: " . $message);
        $send_data = [];

        $send_data['mobile'] = $mobile;
        $send_data['message'] = $message;
        $send_data['token'] = env('QPROXY_SMS_TOKEN', '8759da3d7302494a1e0d3d8f2e246b21');
        $parameters = json_encode($send_data);
        Log::info("SMS parameters: " . $parameters);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('QPROXY_SMS_URL', 'https://sms.ckent.dev/api/sms/v1/send'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [];
        $headers = array(
            "Content-Type: application/json"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $get_sms_status = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        Log::info("SMS API response: " . $get_sms_status);
        if ($curl_error) {
            Log::error("Curl error: " . $curl_error);
        }

        return $get_sms_status;
    }
}
