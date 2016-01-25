<?php

namespace Coduo\PHPMatcher\AST;

final class Expander implements Node
{
    /**
     * @var
     */
    private $name;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->arguments = array();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * @return bool
     */
    public function hasArguments()
    {
        return (boolean) count($this->arguments);
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
