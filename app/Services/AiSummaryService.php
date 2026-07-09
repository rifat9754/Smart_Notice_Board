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

$prompt = "Summarize the following notice as a bulletin for a public notice board.\n\n"

    . "Output format:\n"
    . "AI Summary\n"
    . "• Point 1\n"
    . "• Point 2\n"
    . "• Point 3\n"
    . "• Point 4 (if needed)\n"
    . "• Point 5 (if needed)\n\n"

    . "Rules:\n"
    . "- Start each bullet on a new line.\n"
    . "- Each bullet must begin with the bullet symbol (•).\n"
    . "- Write 3-5 short bullet points.\n"
    . "- Each bullet should contain only one concise sentence.\n"
    . "- Include only the most important information such as purpose, date, time, venue, eligibility, and any required action.\n"
    . "- Do NOT use markdown, asterisks, numbering, headings, or introductory text.\n"
    . "- Return only the formatted summary.\n\n"

    . "Notice:\n"
    . $text;

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