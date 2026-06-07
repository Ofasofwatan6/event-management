<?php

namespace App\Support;

class AvatarHelper
{
    private const COLORS = [
        '#2F7F79', '#1976D2', '#7B1FA2', '#C62828', '#EF6C00',
        '#00897B', '#5D4037', '#455A64', '#6A1B9A', '#AD1457',
    ];

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1).mb_substr($parts[1], 0, 1));
        }

        return strtoupper(mb_substr($name, 0, 2));
    }

    public static function colorFor(string $seed): string
    {
        $index = abs(crc32($seed)) % count(self::COLORS);

        return self::COLORS[$index];
    }
}
