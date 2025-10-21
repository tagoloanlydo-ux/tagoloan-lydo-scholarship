<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS using SMS API
     *
     * @param string $mobile
     * @param string $message
     * @return string
     */
    public function sendSms($mobile, $message)
    {
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
