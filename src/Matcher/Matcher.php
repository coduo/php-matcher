<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

abstract class Matcher implements ValueMatcher
{
    /**
     * @var null|string
     */
    protected $error;

    /**
     * @inheritdoc
     */
    public function getError() : ?string
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function match($value, $pattern): bool
    {
        return $value === $pattern;
    }

    public function clearError() : void
    {
        $this->error = null;
    }
}
