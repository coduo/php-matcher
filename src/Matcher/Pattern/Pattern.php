<?php

namespace Coduo\PHPMatcher\Matcher\Pattern;

interface Pattern
{
    /**
     * @param PatternExpander $expander
     */
    public function addExpander(PatternExpander $expander);

    /**
     * @param $value
     * @return boolean
     */
    public function matchExpanders($value);

    /**
     * Return error message from first expander that doesn't match.
     *
     * @return null|string
     */
    public function getError();

    /**
     * Checks whether a Pattern has added Expander.
     *
     * @param string $expanderName The name of the expander
     *
     * @return bool true if the specified pattern has expander, false otherwise
     */
    public function hasExpander($expanderName);
}
