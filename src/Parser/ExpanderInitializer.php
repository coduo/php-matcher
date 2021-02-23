<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Parser;

use Coduo\PHPMatcher\AST\Expander;
use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Exception\InvalidArgumentException;
use Coduo\PHPMatcher\Exception\InvalidExpanderTypeException;
use Coduo\PHPMatcher\Exception\UnknownExpanderClassException;
use Coduo\PHPMatcher\Exception\UnknownExpanderException;
use Coduo\PHPMatcher\Matcher\Pattern;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

final class ExpanderInitializer
{
    /**
     * @var class-string[]
     */
    private array $expanderDefinitions = [
        Pattern\Expander\After::NAME => Pattern\Expander\After::class,
        Pattern\Expander\Before::NAME => Pattern\Expander\Before::class,
        Pattern\Expander\Contains::NAME => Pattern\Expander\Contains::class,
        Pattern\Expander\NotContains::NAME => Pattern\Expander\NotContains::class,
        Pattern\Expander\Count::NAME => Pattern\Expander\Count::class,
        Pattern\Expander\EndsWith::NAME => Pattern\Expander\EndsWith::class,
        Pattern\Expander\GreaterThan::NAME => Pattern\Expander\GreaterThan::class,
        Pattern\Expander\InArray::NAME => Pattern\Expander\InArray::class,
        Pattern\Expander\IsDateTime::NAME => Pattern\Expander\IsDateTime::class,
        Pattern\Expander\IsInDateFormat::NAME => Pattern\Expander\IsInDateFormat::class,
        Pattern\Expander\IsEmail::NAME => Pattern\Expander\IsEmail::class,
        Pattern\Expander\IsEmpty::NAME => Pattern\Expander\IsEmpty::class,
        Pattern\Expander\IsNotEmpty::NAME => Pattern\Expander\IsNotEmpty::class,
        Pattern\Expander\IsUrl::NAME => Pattern\Expander\IsUrl::class,
        Pattern\Expander\IsIp::NAME => Pattern\Expander\IsIp::class,
        Pattern\Expander\IsTzOffset::NAME => Pattern\Expander\IsTzOffset::class,
        Pattern\Expander\IsTzAbbreviation::NAME => Pattern\Expander\IsTzAbbreviation::class,
        Pattern\Expander\IsTzIdentifier::NAME => Pattern\Expander\IsTzIdentifier::class,
        Pattern\Expander\LowerThan::NAME => Pattern\Expander\LowerThan::class,
        Pattern\Expander\MatchRegex::NAME => Pattern\Expander\MatchRegex::class,
        Pattern\Expander\OneOf::NAME => Pattern\Expander\OneOf::class,
        Pattern\Expander\Optional::NAME => Pattern\Expander\Optional::class,
        Pattern\Expander\StartsWith::NAME => Pattern\Expander\StartsWith::class,
        Pattern\Expander\Repeat::NAME => Pattern\Expander\Repeat::class,
        Pattern\Expander\ExpanderMatch::NAME => Pattern\Expander\ExpanderMatch::class,
        Pattern\Expander\HasProperty::NAME => Pattern\Expander\HasProperty::class,
    ];

    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    public function setExpanderDefinition(string $expanderName, string $expanderFQCN) : void
    {
        if (!\class_exists($expanderFQCN)) {
            throw new UnknownExpanderClassException(\sprintf('Class "%s" does not exists.', $expanderFQCN));
        }

        $this->expanderDefinitions[$expanderName] = $expanderFQCN;
    }

    public function hasExpanderDefinition(string $expanderName) : bool
    {
        return \array_key_exists($expanderName, $this->expanderDefinitions);
    }

    public function getExpanderDefinition(string $expanderName) : string
    {
        if (!$this->hasExpanderDefinition($expanderName)) {
            throw new InvalidArgumentException(\sprintf('Definition for "%s" expander does not exists.', $expanderName));
        }

        return $this->expanderDefinitions[$expanderName];
    }

    public function initialize(Expander $expanderNode) : PatternExpander
    {
        if (!\array_key_exists($expanderNode->getName(), $this->expanderDefinitions)) {
            throw new UnknownExpanderException(\sprintf('Unknown expander "%s"', $expanderNode->getName()));
        }

        $reflection = new \ReflectionClass($this->expanderDefinitions[$expanderNode->getName()]);

        if ($expanderNode->hasArguments()) {
            $arguments = [];

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

        $expander->setBacktrace($this->backtrace);

        return $expander;
    }
}
