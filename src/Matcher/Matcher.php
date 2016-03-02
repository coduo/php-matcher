<?php

namespace Coduo\PHPMatcher\Matcher;

abstract class Matcher implements ValueMatcher
{
    /**
     * @var string|null
     */
    protected $error;

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function match($value, $pattern)
    {
        if ($value === $pattern) {
            return true;
        }
    }
}
