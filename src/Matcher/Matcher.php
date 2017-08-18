<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

abstract class Matcher implements ValueMatcher
{
    protected $error;

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function match($value, $pattern) : bool
    {
        if ($value === $pattern) {
            return true;
        }

        return false;
    }
}
