<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Assert;

final class Json
{
    /**
     * @var string
     */
    private const TRANSFORM_NEW_LINES = '/\r?\n|\r/';

    /**
     * @var string
     */
    private const TRANSFORM_QUOTATION_PATTERN = '/([^"])@([a-zA-Z0-9\.]+)@([^"])/';

    /**
     * @var string
     */
    private const TRANSFORM_QUOTATION_REPLACEMENT = '$1"@$2@"$3';

    public static function isValid($value) : bool
    {
        if (!\is_string($value)) {
            return false;
        }

        $result = \json_decode($value);

        if (\is_float($result) && \is_infinite($result)) {
            return false;
        }

        if (null === $result && JSON_ERROR_NONE !== \json_last_error()) {
            return false;
        }

        return true;
    }

    public static function isValidPattern($value) : bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return self::isValid($value) || self::isValid(self::transformPattern($value));
    }

    public static function transformPattern(string $pattern) : ?string
    {
        return \preg_replace(
            self::TRANSFORM_NEW_LINES,
            '',
            \preg_replace(
                self::TRANSFORM_QUOTATION_PATTERN,
                self::TRANSFORM_QUOTATION_REPLACEMENT,
                $pattern
            )
        );
    }

    public static function reformat(string $json) : string
    {
        return \json_encode(\json_decode($json, true));
    }

    public static function getErrorMessage() : string
    {
        switch (\json_last_error()) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';

            default:
                return 'Unknown error';
        }
    }
}
