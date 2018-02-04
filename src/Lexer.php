<?php

declare(strict_types=1);

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
     */
    protected function getCatchablePatterns() : array
    {
        return [
            '\\.?[a-zA-Z0-9_]+\\(', // expander name
            '[a-zA-Z0-9.]*', // words
            '\\-?[0-9]*\\.?[0-9]*', // numbers
            "'(?:[^']|'')*'", // string between ' character
            '"(?:[^"]|"")*"', // string between " character,
            '@[a-zA-Z0-9\\*]+@', // type pattern
        ];
    }

    /**
     * Lexical non-catchable patterns.
     */
    protected function getNonCatchablePatterns() : array
    {
        return [
            '\\s+',
        ];
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
     */
    protected function getType(&$value) : int
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
            $value = \trim($value, '@');
            return self::T_TYPE_PATTERN;
        }

        if ($this->isStringToken($value)) {
            $value = $this->extractStringValue($value);
            return self::T_STRING;
        }

        if ($this->isBooleanToken($value)) {
            $value = (\strtolower($value) === 'true') ? true : false;
            return self::T_BOOLEAN;
        }

        if ($this->isNullToken($value)) {
            $value = null;
            return self::T_NULL;
        }

        if (\is_numeric($value)) {
            if (\is_string($value)) {
                $value = (\strpos($value, '.') === false) ? (int) $value : (float) $value;
            }

            return self::T_NUMBER;
        }

        if ($this->isExpanderNameToken($value)) {
            $value = \rtrim(\ltrim($value, '.'), '(');
            return self::T_EXPANDER_NAME;
        }

        return $type;
    }

    protected function isStringToken(string $value) : bool
    {
        return \in_array(\substr($value, 0, 1), ['"', "'"]);
    }

    protected function isBooleanToken(string $value) : bool
    {
        return \in_array(\strtolower($value), ['true', 'false'], true);
    }

    protected function isNullToken(string $value) : bool
    {
        return \strtolower($value) === 'null';
    }

    protected function extractStringValue(string $value) : string
    {
        return \trim(\trim($value, "'"), '"');
    }

    protected function isExpanderNameToken(string $value) : bool
    {
        return \substr($value, -1) === '(' && \strlen($value) > 1;
    }

    protected function isTypePatternToken(string $value) : bool
    {
        return \substr($value, 0, 1) === '@' && \substr($value, \strlen($value) - 1, 1) === '@' && \strlen($value) > 1;
    }
}
