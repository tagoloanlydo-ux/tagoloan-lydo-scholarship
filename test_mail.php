<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email from Laravel', function ($mail) {
        $mail->to('test@example.com')
             ->subject('Test Email')
             ->from('cagatanmark17@gmail.com', 'LYDO Scholarship');
    });
    echo "Email sent successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
