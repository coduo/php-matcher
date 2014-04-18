<?php

namespace PHPMatcher\Matcher;

class ChainMatcher implements PropertyMatcher
{
    private $matchers;

    public function __construct(array $matchers = array())
    {
        $this->matchers = $matchers;
    }

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
            }
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
