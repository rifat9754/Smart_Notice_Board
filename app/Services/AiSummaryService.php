<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiSummaryService
{
    public function summarize(string $text): ?string
    {
        $apiKey = config('services.gemini.key');
        if (! $apiKey || trim($text) === '') {
            return null;
        }

        $text = mb_substr($text, 0, 12000); // বড় হলে কেটে নাও

        $prompt = "Summarize this university department notice in 3-4 short, "
            . "clear lines suitable for a display board. Only give the summary:\n\n" . $text;

        try {
            $res = Http::withHeaders(['x-goog-api-key' => $apiKey])
                ->timeout(30)
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                ]);

            if (! $res->successful()) {
                return null;
            }

            $out = $res->json('candidates.0.content.parts.0.text');
            return $out ? trim($out) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}