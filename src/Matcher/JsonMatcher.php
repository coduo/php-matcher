<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Modifier\CaseInsensitive;
use Coduo\PHPMatcher\Matcher\Modifier\IgnoreExtraKeys;
use Coduo\PHPMatcher\Matcher\Modifier\MatcherModifier;
use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;
use Coduo\PHPMatcher\Parser;

final class JsonMatcher extends ModifiableMatcher
{
    const SUPPORTED_MODIFIERS = [
        IgnoreExtraKeys::NAME,
        CaseInsensitive::NAME
    ];

    /**
     * @var ValueMatcher
     */
    private $matcher;

    /**
     * @var Parser
     */
    private $parser;

    public function __construct(ValueMatcher $matcher, Parser $parser)
    {
        $this->matcher = $matcher;
        $this->parser = $parser;
    }

    public function match($value, $pattern) : bool
    {
        if (parent::match($value, $pattern)) {
            return true;
        }

        foreach ($this->parser->parseModifiers($pattern) as $modifier) {
            $this->modify($modifier);
        }

        $pattern = $this->parser->trimModifiers($pattern);

        if (!Json::isValid($value)) {
            $this->error = \sprintf('Invalid given JSON of value. %s', $this->getErrorMessage());
            return false;
        }

        if (!Json::isValidPattern($pattern)) {
            $this->error = \sprintf('Invalid given JSON of pattern. %s', $this->getErrorMessage());
            return false;
        }

        $transformedPattern = Json::transformPattern($pattern);
        $match = $this->matcher->match(\json_decode($value, true), \json_decode($transformedPattern, true));
        if (!$match) {
            $this->error = $this->matcher->getError();
            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        if (\is_string($pattern)) {
            $pattern = $this->parser->trimModifiers($pattern);
        }

        return Json::isValidPattern($pattern);
    }

    public function supportedModifiers(): array
    {
        return \array_keys(self::SUPPORTED_MODIFIERS);
    }

    public function getMatchers(): array
    {
        return [$this->matcher];
    }

    public function applyModifier(MatcherModifier $modifier)
    {
    }

    private function getErrorMessage()
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
