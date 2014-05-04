<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\String;

class ChainMatcher extends Matcher
{
    /**
     * @var array|PropertyMatcher[]
     */
    private $matchers;

    /**
     * @param array|PropertyMatcher[] $matchers
     */
    public function __construct(array $matchers = array())
    {
        $this->matchers = $matchers;
    }

    /**
     * @param PropertyMatcher $matcher
     */
    public function addMatcher(PropertyMatcher $matcher)
    {
        $this->matchers[] = $matcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
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
            $this->error = sprintf(
                'Any matcher from chain can\'t match value "%s" to pattern "%s"',
                new String($value),
                new String($pattern)
            );
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return true;
    }
}
