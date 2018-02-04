<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;

final class ChainMatcher extends Matcher
{
    /**
     * @var ValueMatcher[]
     */
    private $matchers;

    /**
     * @param ValueMatcher[] $matchers
     */
    public function __construct(array $matchers = [])
    {
        $this->matchers = $matchers;
    }

    public function addMatcher(ValueMatcher $matcher)
    {
        $this->matchers[] = $matcher;
    }

    public function match($value, $pattern) : bool
    {
        foreach ($this->matchers as $propertyMatcher) {
            if ($propertyMatcher->canMatch($pattern)) {
                if (true === $propertyMatcher->match($value, $pattern)) {
                    return true;
                }

                $this->error = $propertyMatcher->getError();
            }
        }

        if (!isset($this->error)) {
            $this->error = \sprintf(
                'Any matcher from chain can\'t match value "%s" to pattern "%s"',
                new StringConverter($value),
                new StringConverter($pattern)
            );
        }

        return false;
    }

    public function canMatch($pattern) : bool
    {
        return true;
    }
}
