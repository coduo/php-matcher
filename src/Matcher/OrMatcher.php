<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use function array_map;
use function explode;
use function is_string;
use function preg_match_all;

final class OrMatcher extends Matcher
{
    const MATCH_PATTERN = "/\|\|/";

    /**
     * @var Backtrace
     */
    private $backtrace;

    /**
     * @var ChainMatcher
     */
    private $chainMatcher;

    public function __construct(Backtrace $backtrace, ChainMatcher $chainMatcher)
    {
        $this->chainMatcher = $chainMatcher;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        $patterns = explode('||', $pattern);
        $patterns = array_map('trim', $patterns);

        foreach ($patterns as $childPattern) {
            if ($this->matchChild($value, $childPattern)) {
                $this->backtrace->matcherSucceed(self::class, $value, $pattern);

                return true;
            }
        }

        $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

        return false;
    }

    private function matchChild($value, $pattern) : bool
    {
        if (!$this->chainMatcher->canMatch($pattern)) {
            return false;
        }
        return $this->chainMatcher->match($value, $pattern);
    }

    public function canMatch($pattern): bool
    {
        $result = is_string($pattern) && 0 !== preg_match_all(self::MATCH_PATTERN, $pattern, $matches);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
