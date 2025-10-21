<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';

        $this->info('Current Mail Configuration:');
        $this->info('Default Mailer: ' . config('mail.default'));
        $this->info('From Address: ' . config('mail.from.address'));
        $this->info('From Name: ' . config('mail.from.name'));

        $this->info("\nTesting email to: " . $email);

        try {
            Mail::raw('This is a test email from Laravel.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email');
            });

            $this->info('✅ Email sent successfully!');
            $this->info('Check your logs at: storage/logs/laravel.log');

        } catch (\Exception $e) {
            $this->error('❌ Email failed: ' . $e->getMessage());
        }

        return 0;
    }
}
