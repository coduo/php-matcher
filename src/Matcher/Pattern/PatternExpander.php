<?php

namespace Coduo\PHPMatcher\Matcher\Pattern;

interface PatternExpander
{
    /**
     * @param $value
     * @return boolean
     */
    public function match($value);

    /**
     * @return string|null
     */
    public function getError();

    /**
     * Returns the name by which the expander is identified.
     *
     * @return string The name of the expander
     */
    public function getName();
}
