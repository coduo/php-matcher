<?php

namespace Coduo\PHPMatcher;

use Doctrine\Common\Lexer\AbstractLexer;

final class Lexer extends AbstractLexer
{
    const T_NONE = 1;
    const T_EXPANDER_NAME = 2;
    const T_CLOSE_PARENTHESIS = 3;
    const T_OPEN_CURLY_BRACE    = 4;
    const T_CLOSE_CURLY_BRACE   = 5;
    const T_STRING = 6;
    const T_NUMBER = 7;
    const T_BOOLEAN = 8;
    const T_NULL = 9;
    const T_COMMA  = 10;
    const T_COLON = 11;
    const T_TYPE_PATTERN = 12;

    /**
     * Lexical catchable patterns.
     *
     * @return array
     */
    protected function getCatchablePatterns()
    {
        return array(
            "\\.?[a-zA-Z0-9_]+\\(", // expander name
            "[a-zA-Z0-9.]*", // words
            "\\-?[0-9]*\\.?[0-9]*", // numbers
            "'(?:[^']|'')*'", // string between ' character
            "\"(?:[^\"]|\"\")*\"", // string between " character,
            "@[a-zA-Z0-9\\*]+@", // type pattern
        );
    }

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        return array(
            "\\s+",
        );
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param string $value
     * @return integer
     */
    protected function getType(&$value)
    {
        $type = self::T_NONE;

        if (')' === $value) {
            return self::T_CLOSE_PARENTHESIS;
        }
        if ('{' === $value) {
            return self::T_OPEN_CURLY_BRACE;
        }
        if ('}' === $value) {
            return self::T_CLOSE_CURLY_BRACE;
        }
        if (':' === $value) {
            return self::T_COLON;
        }
        if (',' === $value) {
            return self::T_COMMA;
        }

        if ($this->isTypePatternToken($value)) {
            $value = trim($value, '@');
            return self::T_TYPE_PATTERN;
        }

        if ($this->isStringToken($value)) {
            $value = $this->extractStringValue($value);
            return self::T_STRING;
        }

        if ($this->isBooleanToken($value)) {
            $value = (strtolower($value) === 'true') ? true : false;
            return self::T_BOOLEAN;
        }

        if ($this->isNullToken($value)) {
            $value = null;
            return self::T_NULL;
        }

        if (is_numeric($value)) {
            if (is_string($value)) {
                $value = (strpos($value, '.') === false) ? (int) $value : (float) $value;
            }

            return self::T_NUMBER;
        }

        if ($this->isExpanderNameToken($value)) {
            $value = rtrim(ltrim($value, '.'), '(');
            return self::T_EXPANDER_NAME;
        }

        return $type;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isStringToken($value)
    {
        return in_array(substr($value, 0, 1), array("\"", "'"));
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function isBooleanToken($value)
    {
        return in_array(strtolower($value), array('true', 'false'), true);
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function isNullToken($value)
    {
        return strtolower($value) === 'null';
    }

    /**
     * @param $value
     * @return string
     */
    protected function extractStringValue($value)
    {
        return trim(trim($value, "'"), '"');
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isExpanderNameToken($value)
    {
        return substr($value, -1) === '(' && strlen($value) > 1;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isTypePatternToken($value)
    {
        return substr($value, 0, 1) === '@' && substr($value, strlen($value) - 1, 1) === '@' && strlen($value) > 1;
    }
}
