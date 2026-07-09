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

$prompt = "Summarize the following notice as a bulletin for a public notice board. "
    . "Start the output with 'AI Summary' on the first line. "
    . "Then write 3-5 short bullet points, each starting with a dot (•). "
    . "Each bullet should be one concise sentence. "
    . "Include only the most important information such as purpose, date, time, venue, eligibility, and any required action. "
    . "Do NOT use markdown, asterisks, numbering, or introductory phrases other than 'AI Summary'. "
    . "Return only the summary.\n\n"
    . "Notice:\n" . $text;
        
        

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