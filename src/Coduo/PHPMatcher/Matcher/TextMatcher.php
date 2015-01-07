<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Pattern\TypePattern;
use Coduo\PHPMatcher\Parser;
use Coduo\PHPMatcher\Matcher\Pattern\RegexConverter;
use Coduo\ToString\String;

class TextMatcher extends Matcher
{
    const PATTERN_REGEXP = "/@[a-zA-Z\\.]+@(\\.[a-zA-Z0-9_]+\\([a-zA-Z0-9{},:@\\.\"'\\(\\)]*\\))*/";

    const PATTERN_REGEXP_PLACEHOLDER_TEMPLATE = "__PLACEHOLDER%d__";

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ValueMatcher
     */
    private $matcher;

    /**
     * @param ValueMatcher $matcher
     * @param Parser $parser
     */
    public function __construct(ValueMatcher $matcher, Parser $parser)
    {
        $this->parser = $parser;
        $this->matcher = $matcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_string($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid string.", gettype($value), new String($value));
            return false;
        }

        $patternRegex = $pattern;
        $patternsReplacedWithRegex = $this->replaceTypePatternsWithPlaceholders($patternRegex);
        $patternRegex = $this->prepareRegex($patternRegex);
        $patternRegex = $this->replacePlaceholderWithPatternRegexes($patternRegex, $patternsReplacedWithRegex);

        if (!preg_match($patternRegex, $value, $matchedValues)) {
            $this->error = sprintf("\"%s\" does not match \"%s\" pattern", $value, $pattern);
            return false;
        }

        array_shift($matchedValues); // remove matched string

        if (count($patternsReplacedWithRegex) !== count($matchedValues)) {
            $this->error = "Unexpected TextMatcher error.";
            return false;
        }

        foreach ($patternsReplacedWithRegex as $index => $typePattern) {
            if (!$typePattern->matchExpanders($matchedValues[$index])) {
                $this->error = $typePattern->getError();
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        if (!is_string($pattern)) {
            return false;
        }

        if (mb_strlen($pattern) !== 6 && false !== mb_strpos($pattern, '@null@')) {
            return false;
        }

        return true;
    }

    /**
     * Reaplce each type pattern (@string@.startsWith("lorem")) with placeholder, in order
     * to use preg_quote without destroying pattern & expanders.
     *
     * before replacement: "/users/@integer@.greaterThan(200)/active"
     * after replacement:  "/users/__PLACEHOLDER0__/active"
     *
     * @param string $patternRegex
     * @return TypePattern[]|array
     */
    private function replaceTypePatternsWithPlaceholders(&$patternRegex)
    {
        $patternsReplacedWithRegex = array();
        preg_match_all(self::PATTERN_REGEXP, $patternRegex, $matches);

        foreach ($matches[0] as $index => $typePatternString) {
            $typePattern = $this->parser->parse($typePatternString);
            $patternsReplacedWithRegex[] = $typePattern;
            $patternRegex = str_replace(
                $typePatternString,
                sprintf(self::PATTERN_REGEXP_PLACEHOLDER_TEMPLATE, $index),
                $patternRegex
            );
        }

        return $patternsReplacedWithRegex;
    }


    /**
     * Replace placeholders with type pattern regular expressions
     * before replacement: "/users/__PLACEHOLDER0__/active"
     * after replacement:  "/^\/users\/(\-?[0-9]*)\/active$/"
     *
     * @param $patternRegex
     * @return string
     * @throws \Coduo\PHPMatcher\Exception\UnknownTypeException
     */
    private function replacePlaceholderWithPatternRegexes($patternRegex, array $patternsReplacedWithRegex)
    {
        $regexConverter = new RegexConverter();
        foreach ($patternsReplacedWithRegex as $index => $typePattern) {
            $patternRegex = str_replace(
                sprintf(self::PATTERN_REGEXP_PLACEHOLDER_TEMPLATE, $index),
                $regexConverter->toRegex($typePattern),
                $patternRegex
            );
        }

        return $patternRegex;
    }

    /**
     * Prepare regular expression
     *
     * @param string $patternRegex
     * @return string
     */
    private function prepareRegex($patternRegex)
    {
        return "/^" . preg_quote($patternRegex, '/') . "$/";
    }
}
