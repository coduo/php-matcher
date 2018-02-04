<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Assert;

final class Json
{
    const TRANSFORM_QUOTATION_PATTERN = '/([^"])@([a-zA-Z0-9\.]+)@([^"])/';
    const TRANSFORM_QUOTATION_REPLACEMENT = '$1"@$2@"$3';

    public static function isValid($value) : bool
    {
        if (!\is_string($value)) {
            return false;
        }

        if (null === \json_decode($value) && JSON_ERROR_NONE !== \json_last_error()) {
            return false;
        }

        return true;
    }

    public static function isValidPattern($value) : bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return self::isValid(self::transformPattern($value));
    }

    public static function transformPattern(string $pattern) : string
    {
        return \preg_replace(self::TRANSFORM_QUOTATION_PATTERN, self::TRANSFORM_QUOTATION_REPLACEMENT, $pattern);
    }
}
