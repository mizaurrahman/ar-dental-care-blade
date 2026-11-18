<?php

namespace App\Support;

class BdPhone
{
    public static function normalize(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if (str_starts_with($digits, '880') && strlen($digits) === 13) {
            $digits = substr($digits, 2);
        }

        // After +880 prefix UI: 1XXXXXXXXX (10 digits)
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '+880'.$digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '01')) {
            return '+880'.substr($digits, 1);
        }

        return null;
    }

    public static function isValid(?string $raw): bool
    {
        return self::normalize($raw) !== null;
    }

    public static function local(?string $e164): ?string
    {
        $normalized = self::normalize($e164) ?? $e164;
        if (! $normalized || ! str_starts_with($normalized, '+880')) {
            return null;
        }

        return '0'.substr($normalized, 4);
    }

    /** Digits after +880 for inputs with a disabled +880 prefix (1XXXXXXXXX). */
    public static function subscriber(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $normalized = self::normalize($raw);
        if ($normalized) {
            return substr($normalized, 4);
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return $digits;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '01')) {
            return substr($digits, 1);
        }

        return null;
    }
}
