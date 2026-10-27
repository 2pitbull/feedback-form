<?php

namespace App\Services;

class CaptchaService
{
    private const SESSION_KEY = 'feedback_captcha_answer';

    public function generate(): string
    {
        $first = random_int(12, 49);
        $second = random_int(11, 39);
        $addition = random_int(0, 1) === 0;

        if ($addition) {
            $operator = '+';
            $answer = $first + $second;
        } else {
            if ($second > $first) {
                [$first, $second] = [$second, $first];
            }

            $operator = '−';
            $answer = $first - $second;
        }

        session()->put(self::SESSION_KEY, (string) $answer);

        return "{$first} {$operator} {$second} = ?";
    }

    public function verify(string $answer): bool
    {
        $expected = session()->get(self::SESSION_KEY);

        return is_string($expected)
            && hash_equals($expected, trim($answer));
    }
}
