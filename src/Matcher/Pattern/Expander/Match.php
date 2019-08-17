<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Factory\MatcherFactory;
use Coduo\PHPMatcher\Matcher;

final class Match implements Matcher\Pattern\PatternExpander
{
    const NAME = 'match';

    /**
     * @var Matcher
     */
    private $matcher;
    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (\is_null($this->matcher)) {
            $this->matcher = (new MatcherFactory())->createMatcher();
        }

        return $this->matcher->match($value, $this->pattern);
    }

    public function getError() : ?string
    {
        return $this->matcher->getError();
    }
}
