<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern;

final class TypePattern implements Pattern
{
    private string $type;

    /**
     * @var PatternExpander[]
     */
    private array $expanders = [];

    private ?string $error = null;

    public function __construct(string $type)
    {
        $this->type = $type;
        $this->expanders = [];
    }

    public function is(string $type) : bool
    {
        return \strtolower($this->type) === \strtolower($type);
    }

    public function getType() : string
    {
        return \strtolower($this->type);
    }

    public function addExpander(PatternExpander $expander) : void
    {
        $this->expanders[] = $expander;
    }

    public function matchExpanders($value) : bool
    {
        foreach ($this->expanders as $expander) {
            if (!$expander->match($value)) {
                $this->error = $expander->getError();

                return false;
            }
        }

        return true;
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    public function hasExpander(string $expanderName) : bool
    {
        foreach ($this->expanders as $expander) {
            if ($expander::is($expanderName)) {
                return true;
            }
        }

        return false;
    }
}
