<?php

declare(strict_types=1);

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

    public function match($value, $pattern) : bool
    {
        if ($value === $pattern) {
            return true;
        }

        return false;
    }
}
