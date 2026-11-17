<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class WhatsAppNotifier
{
    public function send(?string $destination, string $message): void
    {
        // Never send messages to real phone numbers from a public demo installation.
        if (config('demo.enabled')) {
            return;
        }
        $apiKey = config('services.nusasms.key');
        if (!$apiKey || !$destination) {
            return;
        }
        try {
            Http::timeout(8)->connectTimeout(3)->withHeaders(['APIKey' => $apiKey])
                ->asJson()->post('https://api.nusasms.com/nusasms_api/1.0/whatsapp/message', [
                    'destination' => $destination,
                    'message' => $message,
                ])->throw();
        } catch (\Throwable $error) {
            // Never roll back a completed reservation because a third-party notification failed.
            Log::warning('Reservation notification could not be delivered', ['error' => $error->getMessage()]);
        }
    }
}
