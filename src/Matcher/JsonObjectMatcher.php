<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class JsonObjectMatcher extends Matcher
{
    /**
     * @var string
     */
    public const JSON_PATTERN = 'json';

    private Backtrace $backtrace;

    private Parser $parser;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->backtrace = $backtrace;
        $this->parser = $parser;
    }

    public function match($value, $pattern) : bool
    {
        if (!$this->isJsonPattern($pattern)) {
            $this->error = \sprintf('%s "%s" is not a valid json.', \gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (!Json::isValid($value) && null !== $value && !\is_array($value)) {
            $this->error = \sprintf('Invalid given JSON of value. %s', Json::getErrorMessage());
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        return $this->allExpandersMatch($value, $pattern);
    }

    public function canMatch($pattern) : bool
    {
        $result = \is_string($pattern) && $this->isJsonPattern($pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }

    private function isJsonPattern($pattern) : bool
    {
        if (!\is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::JSON_PATTERN);
    }

    private function allExpandersMatch($value, $pattern) : bool
    {
        $typePattern = $this->parser->parse($pattern);

        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }
}
