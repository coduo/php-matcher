<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\AST;

final class Pattern implements Node
{
    private $type;

    private $expanders;

    public function __construct(Type $type)
    {
        $this->expanders = [];
        $this->type = $type;
    }

    public function getType() : Type
    {
        return $this->type;
    }

    public function hasExpanders() : bool
    {
        return (boolean) \count($this->expanders);
    }

    /**
     * @return Expander[]
     */
    public function getExpanders() : array
    {
        return $this->expanders;
    }

    public function addExpander(Expander $expander)
    {
        $this->expanders[] = $expander;
    }
}
