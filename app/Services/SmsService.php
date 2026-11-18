<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $to, string $message): void
    {
        $driver = config('services.sms.driver', 'log');

        if ($driver === 'log') {
            Log::info('SMS sent', ['to' => $to, 'message' => $message]);

            return;
        }

        // Hook for production BD gateway (SSL Wireless / BulkSMSBD / Twilio)
        Log::warning('SMS driver not fully configured; logged instead', [
            'driver' => $driver,
            'to' => $to,
            'message' => $message,
        ]);
    }
}
