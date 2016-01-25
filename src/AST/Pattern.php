<?php

namespace Coduo\PHPMatcher\AST;

final class Pattern implements Node
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @var Expander[]|array
     */
    private $expanders;

    /**
     * @param Type $type
     */
    public function __construct(Type $type)
    {
        $this->expanders = array();
        $this->type = $type;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function hasExpanders()
    {
        return (boolean) count($this->expanders);
    }

    /**
     * @return Expander[]|array
     */
    public function getExpanders()
    {
        return $this->expanders;
    }

    /**
     * @param Expander $expander
     */
    public function addExpander(Expander $expander)
    {
        $this->expanders[] = $expander;
    }
}
