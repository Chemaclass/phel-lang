<?php

namespace Phel\SourceMap;

use Exception;

class VLQ
{
    private array $integerToChar = [];
    private array $charToInteger = [];

    private const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';

    // Cache with precomputed values
    private static $encoderCache = [
        -10 => "V",
        -9 => "T",
        -8 => "R",
        -7 => "P",
        -6 => "N",
        -5 => "L",
        -4 => "J",
        -3 => "H",
        -2 => "F",
        -1 => "D",
        0 => "A",
        1 => "C",
        2 => "E",
        3 => "G",
        4 => "I",
        5 => "K",
        6 => "M",
        7 => "O",
        8 => "Q",
        9 => "S",
    ];

    public function __construct()
    {
        $charLength = strlen(self::CHARS);
        for ($i = 0; $i < $charLength; $i++) {
            $c = self::CHARS[$i];
            $this->integerToChar[$i] = $c;
            $this->charToInteger[$c] = $i;
        }
    }

    public function decode(string $string): array
    {
        $result = [];
        $shift = 0;
        $value = 0;
        $strlen = strlen($string);

        for ($i = 0; $i < $strlen; $i++) {
            $char = $string[$i];
            if (!isset($this->charToInteger[$char])) {
                throw new Exception('Invalid character: ' . $char);
            }

            $integer = $this->charToInteger[$char];
            $hasContinuationBit = $integer & 32;

            $integer &= 31;
            $value += $integer << $shift;

            if ($hasContinuationBit) {
                $shift += 5;
            } else {
                $shouldNegate = $value & 1;
                $value = $this->bitShiftRightWithZero($value, 1);

                if ($shouldNegate) {
                    $result[] = ($value === 0 ? -0x80000000 : -$value);
                } else {
                    $result[] = $value;
                }

                $value = $shift = 0;
            }
        }

        return $result;
    }

    public function encodeIntegers(array $numbers): string
    {
        $result = '';
        foreach ($numbers as $number) {
            $result .= $this->encodeInteger($number);
        }
        return $result;
    }

    public function encodeInteger(int $num): string
    {
        $originalNum = $num;
        if (isset(self::$encoderCache[$originalNum])) {
            return self::$encoderCache[$originalNum];
        }

        $result = '';

        if ($num < 0) {
            $num = (-$num << 1) | 1;
        } else {
            $num <<= 1;
        }

        do {
            $clamped = $num & 31;
            $num = $this->bitShiftRightWithZero($num, 5);

            if ($num > 0) {
                $clamped |= 32;
            }

            $result .= $this->integerToChar[$clamped];
        } while ($num > 0);

        self::$encoderCache[$originalNum] = $result;

        return $result;
    }

    private function bitShiftRightWithZero(int $v, int $n): int
    {
        return ($v & 0xFFFFFFFF) >> ($n & 0x1F);
    }
}
