<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\AST;

final class Expander implements Node
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed[]
     */
    private $arguments;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->arguments = [];
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function addArgument($argument) : void
    {
        $this->arguments[] = $argument;
    }

    public function hasArguments() : bool
    {
        return (bool) \count($this->arguments);
    }

    public function getArguments() : array
    {
        return $this->arguments;
    }
}
