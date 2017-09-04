<?php

namespace Coduo\PHPMatcher\Matcher\Pattern;

final class TypePattern implements Pattern
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var PatternExpander[]|array
     */
    private $expanders;

    /**
     * @var null|string
     */
    private $error;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->expanders = array();
    }

    /**
     * @param $type
     * @return boolean
     */
    public function is($type)
    {
        return strtolower($this->type) === strtolower($type);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return strtolower($this->type);
    }

    /**
     * {@inheritdoc}
     */
    public function addExpander(PatternExpander $expander)
    {
        $this->expanders[] = $expander;
    }

    /**
     * {@inheritdoc}
     */
    public function matchExpanders($value)
    {
        foreach ($this->expanders as $expander) {
            if (!$expander->match($value)) {
                $this->error = $expander->getError();
                return false;
            }
        }

        return true;
    }

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
    public function hasExpander($expanderName)
    {
        foreach ($this->expanders as $expander) {
            if ($expander::is($expanderName)) {
                return true;
            }
        }

        return false;
    }
}
