<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private function getAccessToken(): ?string
    {
        try {
            $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));

            // Render-এ JSON content সরাসরি env-এ থাকলে
            if (env('FIREBASE_CREDENTIALS_JSON')) {
                $credentials = json_decode(env('FIREBASE_CREDENTIALS_JSON'), true);
            } elseif (file_exists($credentialsPath)) {
                $credentials = json_decode(file_get_contents($credentialsPath), true);
            } else {
                Log::error('FCM: credentials not found');
                return null;
            }

            $client = new GoogleClient();
            $client->setAuthConfig($credentials);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();

            return $client->getAccessToken()['access_token'] ?? null;
        } catch (\Throwable $e) {
            Log::error('FCM token error: ' . $e->getMessage());
            return null;
        }
    }

    public function send(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();
        $projectId   = env('FIREBASE_PROJECT_ID');

        if (!$accessToken || !$projectId || !$deviceToken) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => empty($data) ? (object)[] : array_map('strval', $data),
                    'android' => [
                        'priority' => 'high',
                        'notification' => ['sound' => 'default'],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('FCM send failed: ' . $response->body());
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('FCM send exception: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToMany(array $tokens, string $title, string $body, array $data = []): void
    {
        foreach (array_filter(array_unique($tokens)) as $token) {
            $this->send($token, $title, $body, $data);
        }
    }
}