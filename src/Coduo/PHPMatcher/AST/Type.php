<?php

namespace Coduo\PHPMatcher\AST;

class Type implements Node
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function __toString()
    {
        return $this->type;
    }
}
