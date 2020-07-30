<?php

declare(strict_types = 1);

namespace Rentalhost\Vanilla\Namefixer;

class Namefixer
{
    public static function fix(?string $name, ?bool $forceFirstUppercase = null): ?string
    {
        if (!$name) {
            return $name;
        }

        $normalizedNames          = preg_split('/[\s\xA0]+/u', preg_replace('/\.+/u', '. ', preg_replace('/^[\s\xA0]+|[\s\xA0]+$/u', null, $name)));
        $normalizedNamesLastIndex = count($normalizedNames) - 1;

        $processedName = rtrim(implode(' ', array_map(static function (string $nameOriginal, int $nameIndex) use ($normalizedNamesLastIndex) {
            $nameLower            = mb_strtolower($nameOriginal);
            $nameLowerClean       = preg_replace('/\.+$/', null, $nameLower);
            $nameLowerCleanLength = mb_strlen($nameLowerClean);

            if ($nameLowerCleanLength === 1) {
                // Eg. "John d" => "John D."
                if ($nameOriginal !== 'e') {
                    return mb_strtoupper($nameLowerClean) . '.';
                }

                // Eg. "John e Doe" => "John e Doe" (same), but...
                //     "John e"     => "John E."
                //     "e Doe"      => "E. Doe"
                if ($nameIndex === 0 ||
                    $nameIndex === $normalizedNamesLastIndex) {
                    return mb_strtoupper($nameLowerClean) . '.';
                }
            }

            // Eg. "John jr"  => "John Jr.",
            //     "John jr." => "John Jr."
            if ($nameLowerClean === 'jr') {
                return self::upperFirstLetterOnly($nameLowerClean) . '.';
            }

            // Eg. "john mc doe" => "John Mc Doe"
            if ($nameLowerClean === 'mc') {
                return self::upperFirstLetterOnly($nameLowerClean);
            }

            // Eg. "john dd" => "John D. D."
            if (preg_match('/^[^aeiouy\x{00E0}-\x{00E6}\x{00E8}-\x{00EF}\x{00F2}-\x{00F6}\x{00F9}-\x{00FD}\x{00FF}]+$/ui', $nameLowerClean)) {
                return implode('. ', mb_str_split(mb_strtoupper($nameLowerClean))) . '.';
            }

            // Eg. "John dos Doe" => "John dos Doe" (keeps "dos" lowercased)
            if (in_array($nameLowerClean, [ 'e', 'de', 'do', 'da', 'dos', 'das', 'el', 'la', 'lo', 'di', 'van', 'der', 'den' ], true)) {
                return mb_strtolower($nameLowerClean);
            }

            // Eg. "John mcdoe" => "John McDoe"
            if (mb_strpos($nameLowerClean, 'mc') === 0) {
                return self::upperFirstLetterOnly(mb_substr($nameLowerClean, 0, 2)) .
                       self::upperFirstLetterOnly(mb_substr($nameLowerClean, 2));
            }

            // Eg. "john doe" => "John Doe" (general rule)
            return self::upperFirstLetterOnly($nameLowerClean);
        }, $normalizedNames, array_keys($normalizedNames))));

        // Eg. "do Doe" => "Do Doe" (when forced)
        if ($forceFirstUppercase) {
            return mb_strtoupper(mb_substr($processedName, 0, 1)) .
                   mb_substr($processedName, 1);
        }

        return $processedName;
    }

    public static function isValid(?string $name): bool
    {
        return (bool) preg_match('/^[\p{L}\s\xA0.\']+$/u', (string) $name);
    }

    public static function upperFirstLetterOnly(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        return mb_strtoupper(mb_substr($value, 0, 1)) .
               mb_strtolower(mb_substr($value, 1));
    }
}
