<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url');
        $this->username = config('services.whatsapp.username');
        $this->password = config('services.whatsapp.password');
    }

    /**
     * 🔑 LOGIN (dapat access + refresh token)
     */
    private function login()
    {
        $response = Http::post($this->baseUrl . '/auth/token', [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (!$response->successful()) {
            Log::error('WA Login Failed', [
                'response' => $response->body()
            ]);
            throw new \Exception('Gagal login WA Gateway');
        }

        $data = $response->json();

        Cache::put('wa_access_token', $data['access_token'], now()->addSeconds($data['expires_in'] - 60));
        Cache::put('wa_refresh_token', $data['refresh_token'], now()->addDays(6));

        return $data['access_token'];
    }

    /**
     * 🔄 REFRESH TOKEN
     */
    private function refreshToken()
    {
        $refreshToken = Cache::get('wa_refresh_token');

        if (!$refreshToken) {
            return $this->login();
        }

        $response = Http::post($this->baseUrl . '/auth/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        if (!$response->successful()) {
            Log::warning('WA Refresh Failed, fallback login');
            return $this->login();
        }

        $data = $response->json();

        Cache::put('wa_access_token', $data['access_token'], now()->addSeconds($data['expires_in'] - 60));
        Cache::put('wa_refresh_token', $data['refresh_token'], now()->addDays(6));

        return $data['access_token'];
    }

    /**
     * 🎯 Ambil token valid
     */
    private function getToken()
    {
        $token = Cache::get('wa_access_token');

        if ($token) {
            return $token;
        }

        return $this->refreshToken();
    }

    /**
     * 📞 Format nomor
     */
    private function formatNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (substr($number, 0, 1) === '0') {
            return '62' . substr($number, 1);
        }

        return $number;
    }

    /**
     * 📲 SEND MESSAGE (AUTO HANDLE TOKEN)
     */
    public function sendMessage($numbers, $message)
    {
        try {
            $numbers = is_array($numbers) ? $numbers : [$numbers];
            $numbers = array_map([$this, 'formatNumber'], $numbers);

            // 🔁 attempt 1
            $token = $this->getToken();

            $response = Http::withToken($token)
                ->post($this->baseUrl . '/send', [
                    'numbers' => $numbers,
                    'message' => $message,
                ]);

            // 🔥 kalau token expired
            if ($response->status() === 401) {
                Log::warning('WA Token expired, refreshing...');

                $token = $this->refreshToken();

                $response = Http::withToken($token)
                    ->post($this->baseUrl . '/send', [
                        'numbers' => $numbers,
                        'message' => $message,
                    ]);
            }

            if (!$response->successful()) {
                Log::error('WA Send Failed', [
                    'numbers' => $numbers,
                    'response' => $response->body()
                ]);
            } else {
                Log::info('WA Sent', [
                    'numbers' => $numbers,
                    'message' => $message
                ]);
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('WA Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📩 Disposisi helper
     */
    public function sendDisposition($user, $mail, $fromUser, $roleLabel = 'User')
    {
        if (!$user || !$user->whatsapp_number) return;

        $message = "*📩 DISPOSISI BARU*\n\n"
            . "No Surat: {$mail->nomor_register}\n"
            . "Perihal: {$mail->perihal}\n"
            . "Dari: {$fromUser->name}\n"
            . "Untuk: {$roleLabel}";

        return $this->sendMessage($user->whatsapp_number, $message);
    }

    /**
     * 📄 CC helper
     */
    public function sendCc($user, $mail, $addedBy)
    {
        if (!$user || !$user->whatsapp_number) return;

        $message = "*📄 TEMBUSAN SURAT*\n\n"
            . "No Surat: {$mail->nomor_register}\n"
            . "Perihal: {$mail->perihal}\n"
            . "Dari: {$addedBy->name}";

        return $this->sendMessage($user->whatsapp_number, $message);
    }
}