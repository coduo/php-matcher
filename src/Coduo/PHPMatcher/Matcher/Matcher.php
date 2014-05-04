<?php

namespace Coduo\PHPMatcher\Matcher;

abstract class Matcher implements PropertyMatcher
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
}
