<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token', env('FONNTE_TOKEN', ''));
    }

    /**
     * Kirim pesan WhatsApp ke nomor tujuan via Fonnte.
     *
     * @param  string  $target   Nomor tujuan (format 08xxx atau 628xxx)
     * @param  string  $message  Isi pesan yang akan dikirim
     * @return bool
     */
    public function send(string $target, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            if (!$response->successful()) {
                Log::error('Fonnte send failed', [
                    'target'   => $target,
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }

            Log::info('Fonnte message sent', ['target' => $target]);
            return true;

        } catch (\Exception $e) {
            Log::error('Fonnte exception: ' . $e->getMessage(), ['target' => $target]);
            return false;
        }
    }
}
