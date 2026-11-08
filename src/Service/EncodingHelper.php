<?php

namespace Btinet\Untis\Service;

use Btinet\Untis\Enum\Substitution\UntisBit;
use Btinet\Untis\Enum\Substitution\UntisType;
use DateTimeImmutable;
use InvalidArgumentException;

class EncodingHelper
{

    public function strToStr(?string $value, bool $splitValues = false, bool $sort = true, string $separator = "~"): array|string
    {
        $output = '';

        if ($value === null || $value === '') {
            if ($splitValues) { // Neue Unterbedingung
                return [];
            } else {
                return $output;
            }
        }
        $output = trim($value);

        if (str_starts_with($output, "\xEF\xBB\xBF")) {
            $output = substr($output, 3);
        }
        if (!mb_check_encoding($output, 'UTF-8')) {
            $output = mb_convert_encoding($output, 'UTF-8', 'Windows-1252');
        }

        // Z. B. 7.1~7.2~7.3 oder -~+
        if ($splitValues) {
            $parts = explode($separator, $output);
            $output = array_map('trim', $parts);

            // 7.1 vor 10B
            if ($sort) {
                natsort($output);
                $output = array_values($output);
            }
        }
        return $output;
    }

    public function strToPositiveInt(?string $str): int
    {
        $str = trim($str ?? '');
        if ($str === '') {
            $str = '0';
        }
        if (preg_match('/^\d+$/', $str) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid bit value "%s".', $str));
        }
        return (int)$str;
    }

    public function strToInt(?string $str): int
    {
        $str = trim($str ?? '');
        if ($str === '') {
            $str = '0';
        }
        return (int)round((float)$str);
    }

    public function strToId(?string $str): ?int
    {
        $str = trim($str ?? '');
        if ($str === '') {
            return null;
        }
        if (preg_match('/^\d+$/', $str) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid id value "%s".', $str));
        }
        return (int) $str;
    }

    public function strToDateTimeImmutable(?string $str, string $format = 'Ymd'): ?DateTimeImmutable
    {
        if ($str === null || $str === '') {
            return null;
        }
        $dateTime = DateTimeImmutable::createFromFormat($format, $str);

        if ($dateTime === false) {
            throw new InvalidArgumentException(sprintf('Invalid date format "%s".', $format));
        }
        if (!str_contains($format, 'H') && !str_contains($format, 'i') && !str_contains($format, 's')) {
            $dateTime = $dateTime->setTime(0, 0, 0);
        }
        return $dateTime;
    }

    public function strToBitArray(?string $str): array
    {
        $str = trim($str ?? '');
        if ($str === '') {
            $str = '0';
        }
        if (preg_match('/^\d+$/', $str) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid bit value "%s".', $str));
        }
        return UntisBit::getActiveFlags($str);
    }

    public function strToType(?string $str): ?UntisType
    {
        $str = trim($str ?? '');
        return UntisType::tryFromChar($str);
    }

}