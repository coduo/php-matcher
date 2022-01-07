<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use function preg_quote;
use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Exception\UnknownTypeException;
use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;
use Coduo\PHPMatcher\Matcher\Pattern\Assert\Xml;
use Coduo\PHPMatcher\Matcher\Pattern\RegexConverter;
use Coduo\PHPMatcher\Matcher\Pattern\TypePattern;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class TextMatcher extends Matcher
{
    /**
     * @var string
     */
    public const PATTERN_REGEXP = "/@[a-zA-Z\\.]+@(\\.\\w+\\([a-zA-Z0-9{},:@\\.\"'\\(\\)]*\\))*/";

    /**
     * @var string
     */
    public const PATTERN_REGEXP_PLACEHOLDER_TEMPLATE = '__PLACEHOLDER%d__';

    private Parser $parser;

    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->parser = $parser;
        $this->backtrace = $backtrace;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!\is_string($value)) {
            $this->error = \sprintf('%s "%s" is not a valid string.', \gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

            return false;
        }

        $patternRegex = $pattern;
        $patternsReplacedWithRegex = $this->replaceTypePatternsWithPlaceholders($patternRegex);
        $patternRegex = $this->prepareRegex($patternRegex);

        try {
            $patternRegex = $this->replacePlaceholderWithPatternRegexes($patternRegex, $patternsReplacedWithRegex);
        } catch (UnknownTypeException $unknownTypeException) {
            $this->error = \sprintf('Type pattern "%s" is not supported by TextMatcher.', $unknownTypeException->getType());
            $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

            return false;
        }

        if (!\preg_match($patternRegex, $value, $matchedValues)) {
            $this->error = \sprintf('"%s" does not match "%s" pattern', $value, $pattern);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

            return false;
        }

        \array_shift($matchedValues); // remove matched string

        if (\count($patternsReplacedWithRegex) !== \count($matchedValues)) {
            $this->error = 'Unexpected TextMatcher error.';
            $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

            return false;
        }

        foreach ($patternsReplacedWithRegex as $index => $typePattern) {
            if (!$typePattern->matchExpanders($matchedValues[$index])) {
                $this->error = $typePattern->getError();
                $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

                return false;
            }
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern) : bool
    {
        if (!\is_string($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        if (Json::isValidPattern($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        if (Xml::isValid($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        $this->backtrace->matcherCanMatch(self::class, $pattern, true);

        return true;
    }

    /**
     * Replace each type pattern (@string@.startsWith("lorem")) with placeholder, in order
     * to use preg_quote without destroying pattern & expanders.
     *
     * before replacement: "/users/@integer@.greaterThan(200)/active"
     * after replacement:  "/users/__PLACEHOLDER0__/active"
     *
     * @param string $patternRegex
     *
     * @return array|TypePattern[]
     */
    private function replaceTypePatternsWithPlaceholders(string &$patternRegex) : array
    {
        $patternsReplacedWithRegex = [];
        \preg_match_all(self::PATTERN_REGEXP, $patternRegex, $matches);

        foreach ($matches[0] as $index => $typePatternString) {
            $typePattern = $this->parser->parse($typePatternString);
            $patternsReplacedWithRegex[] = $typePattern;
            $patternRegex = \str_replace(
                $typePatternString,
                \sprintf(self::PATTERN_REGEXP_PLACEHOLDER_TEMPLATE, $index),
                $patternRegex
            );
        }

        return $patternsReplacedWithRegex;
    }

    /**
     * Replace placeholders with type pattern regular expressions
     * before replacement: "/users/__PLACEHOLDER0__/active"
     * after replacement:  "/^\/users\/(\-?[0-9]*)\/active$/".
     *
     * @param string $patternRegex
     *
     * @throws UnknownTypeException
     *
     * @return string
     */
    private function replacePlaceholderWithPatternRegexes(string $patternRegex, array $patternsReplacedWithRegex) : string
    {
        $regexConverter = new RegexConverter();

        foreach ($patternsReplacedWithRegex as $index => $typePattern) {
            $patternRegex = \str_replace(
                \sprintf(self::PATTERN_REGEXP_PLACEHOLDER_TEMPLATE, $index),
                $regexConverter->toRegex($typePattern),
                $patternRegex
            );
        }

        return $patternRegex;
    }

    /**
     * Prepare regular expression.
     *
     * @param string $patternRegex
     *
     * @return string
     */
    private function prepareRegex(string $patternRegex) : string
    {
        return '/^' . \preg_quote($patternRegex, '/') . '$/';
    }
}
