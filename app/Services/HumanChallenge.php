<?php

namespace App\Services;

class HumanChallenge
{
    public function create(): array
    {
        $paragraphs = require resource_path('human-check/paragraphs.php');
        $index = random_int(0, count($paragraphs) - 1);
        $lines = $paragraphs[$index];
        // Ten distinct positional questions; only one leaves the server.
        $positions = [[1, 2], [2, 3], [3, 1], [4, 4], [5, 4], [6, 1], [6, 3], [7, 2], [1, 5], [3, 5]];
        [$line, $word] = $positions[random_int(0, 9)];

        return [
            'id' => bin2hex(random_bytes(24)),
            'image' => sprintf('/human-check-images/paragraph-%02d.png', $index + 1),
            'question' => "{$line}. satırdaki {$word}. kelimeyi yazın.",
            'answer' => $this->normalize(explode(' ', $lines[$line - 1])[$word - 1]),
            'expires' => time() + config('human-check.challenge_seconds'),
        ];
    }

    public function normalize(string $answer): string
    {
        return mb_strtolower(str_replace(['İ', 'I'], ['i', 'ı'], trim($answer, " \t\n\r\0\x0B.,;:!?")), 'UTF-8');
    }
}
