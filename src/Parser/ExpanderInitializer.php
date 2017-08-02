<?php

namespace Coduo\PHPMatcher\Parser;

use Coduo\PHPMatcher\AST\Expander as ExpanderNode;
use Coduo\PHPMatcher\Exception\InvalidArgumentException;
use Coduo\PHPMatcher\Exception\InvalidExpanderTypeException;
use Coduo\PHPMatcher\Exception\UnknownExpanderClassException;
use Coduo\PHPMatcher\Exception\UnknownExpanderException;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\PHPMatcher\Matcher\Pattern\Expander;

final class ExpanderInitializer
{
    /**
     * @var array
     */
    private $expanderDefinitions = [
        Expander\Contains::NAME => Expander\Contains::class,
        Expander\Count::NAME => Expander\Count::class,
        Expander\EndsWith::NAME => Expander\EndsWith::class,
        Expander\GreaterThan::NAME => Expander\GreaterThan::class,
        Expander\InArray::NAME => Expander\InArray::class,
        Expander\IsDateTime::NAME => Expander\IsDateTime::class,
        Expander\IsEmail::NAME => Expander\IsEmail::class,
        Expander\IsEmpty::NAME => Expander\IsEmpty::class,
        Expander\IsNotEmpty::NAME => Expander\IsNotEmpty::class,
        Expander\IsUrl::NAME => Expander\IsUrl::class,
        Expander\LowerThan::NAME => Expander\LowerThan::class,
        Expander\MatchRegex::NAME => Expander\MatchRegex::class,
        Expander\OneOf::NAME => Expander\OneOf::class,
        Expander\Optional::NAME => Expander\Optional::class,
        Expander\StartsWith::NAME => Expander\StartsWith::class,
    ];

    /**
     * @param string $expanderName
     * @param string $expanderFQCN Fully-Qualified Class Name that implements PatternExpander interface
     * @throws UnknownExpanderClassException
     */
    public function setExpanderDefinition($expanderName, $expanderFQCN)
    {
        if (!class_exists($expanderFQCN)) {
            throw new UnknownExpanderClassException(sprintf("Class \"%s\" does not exists.", $expanderFQCN));
        }

        $this->expanderDefinitions[$expanderName] = $expanderFQCN;
    }

    /**
     * @param $expanderName
     * @return bool
     */
    public function hasExpanderDefinition($expanderName)
    {
        return array_key_exists($expanderName, $this->expanderDefinitions);
    }

    /**
     * @param $expanderName
     * @return string
     * @throws InvalidArgumentException
     */
    public function getExpanderDefinition($expanderName)
    {
        if (!$this->hasExpanderDefinition($expanderName)) {
            throw new InvalidArgumentException(sprintf("Definition for \"%s\" expander does not exists.", $expanderName));
        }

        return $this->expanderDefinitions[$expanderName];
    }

    /**
     * @param ExpanderNode $expanderNode
     * @throws InvalidExpanderTypeException
     * @throws UnknownExpanderException
     * @return PatternExpander
     */
    public function initialize(ExpanderNode $expanderNode)
    {
        if (!array_key_exists($expanderNode->getName(), $this->expanderDefinitions)) {
            throw new UnknownExpanderException(sprintf("Unknown expander \"%s\"", $expanderNode->getName()));
        }

        $reflection = new \ReflectionClass($this->expanderDefinitions[$expanderNode->getName()]);

        if ($expanderNode->hasArguments()) {
            $arguments = array();
            foreach ($expanderNode->getArguments() as $argument) {
                $arguments[] = ($argument instanceof ExpanderNode)
                    ? $this->initialize($argument)
                    : $argument;
            }

            $expander = $reflection->newInstanceArgs($arguments);
        } else {
            $expander = $reflection->newInstance();
        }

        if (!$expander instanceof PatternExpander) {
            throw new InvalidExpanderTypeException();
        }

        return $expander;
    }
}
