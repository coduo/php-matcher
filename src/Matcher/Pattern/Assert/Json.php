<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Assert;

final class Json
{
    const TRANSFORM_QUOTATION_PATTERN = '/([^"])@([a-zA-Z0-9\.]+)@([^"])/';
    const TRANSFORM_QUOTATION_REPLACEMENT = '$1"@$2@"$3';

    /**
     * @param string $value
     * @return bool
     */
    public static function isValid($value)
    {
        if (!is_string($value)) {
            return false;
        }

        if (null === json_decode($value) && JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }

        return true;
    }

    /**
     * Before checking json it wraps type patterns (@type@) with quotes ("@type@")
     *
     * @param string $value
     * @return bool
     */
    public static function isValidPattern($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return self::isValid(self::transformPattern($value));
    }

    /**
     * Wraps placeholders which arent wrapped with quotes yet
     *
     * @param $pattern
     * @return mixed
     */
    public static function transformPattern($pattern)
    {
        return preg_replace(self::TRANSFORM_QUOTATION_PATTERN, self::TRANSFORM_QUOTATION_REPLACEMENT, $pattern);
    }
}
