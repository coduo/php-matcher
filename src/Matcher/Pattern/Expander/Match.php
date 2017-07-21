<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Match implements PatternExpander
{
    /**
     * @var null|string
     */
    private $error;

    /**
     * @var
     */
    private $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param $value
     * @return boolean
     */
    public function match($value)
    {
        if (!is_array($value)) {
            $this->error = sprintf("Match expander require \"array\", got \"%s\".", new StringConverter($value));
            return false;
        }

        $match = true;
        $matcher = (new SimpleFactory)->createMatcher();
        foreach ($value as $singleRowValue) {
            if (!$matcher->match($singleRowValue, $this->value)) {
                $match = false;
                $this->error = $matcher->getError();
            }
        }
        unset($matcher);

        return $match;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }
}
