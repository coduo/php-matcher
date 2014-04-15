<?php

namespace JsonMatcher\Matcher;

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
            if (false === $propertyMatcher->match($value, $pattern)) {
                return false;
            }
        }

        return true;
    }

    public function canMatch($pattern)
    {
        return true;
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'chain';
    }
}
