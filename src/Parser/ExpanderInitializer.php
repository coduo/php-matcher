<?php

namespace Coduo\PHPMatcher\Parser;

use Coduo\PHPMatcher\AST\Expander;
use Coduo\PHPMatcher\Exception\InvalidArgumentException;
use Coduo\PHPMatcher\Exception\InvalidExpanderTypeException;
use Coduo\PHPMatcher\Exception\UnknownExpanderClassException;
use Coduo\PHPMatcher\Exception\UnknownExpanderException;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

final class ExpanderInitializer
{
    /**
     * @var array
     */
    private $expanderDefinitions = array(
        "startsWith" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\StartsWith",
        "endsWith" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\EndsWith",
        "isNotEmpty" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\IsNotEmpty",
        "isEmpty" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\IsEmpty",
        "isDateTime" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\IsDateTime",
        "isEmail" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\IsEmail",
        "isUrl" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\IsUrl",
        "lowerThan" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\LowerThan",
        "greaterThan" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\GreaterThan",
        "inArray" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\InArray",
        "count" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\Count",
        "contains" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\Contains",
        "matchRegex" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\MatchRegex",

        "oneOf" => "Coduo\\PHPMatcher\\Matcher\\Pattern\\Expander\\OneOf"
    );

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
     * @param Expander $expanderNode
     * @throws InvalidExpanderTypeException
     * @throws UnknownExpanderException
     * @return PatternExpander
     */
    public function initialize(Expander $expanderNode)
    {
        if (!array_key_exists($expanderNode->getName(), $this->expanderDefinitions)) {
            throw new UnknownExpanderException(sprintf("Unknown expander \"%s\"", $expanderNode->getName()));
        }

        $reflection = new \ReflectionClass($this->expanderDefinitions[$expanderNode->getName()]);

        if ($expanderNode->hasArguments()) {
            $arguments = array();
            foreach ($expanderNode->getArguments() as $argument) {
                $arguments[] = ($argument instanceof Expander)
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
