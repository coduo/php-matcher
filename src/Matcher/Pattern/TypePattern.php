<?php declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern;

use Coduo\PHPMatcher\Parser\ExpanderInitializer;

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
     * @var ExpanderInitializer
     */
    private $expanderInitializer;

    /**
     * @param string $type
     * @param ExpanderInitializer $expanderInitializer
     */
    public function __construct($type, ExpanderInitializer $expanderInitializer)
    {
        $this->type = $type;
        $this->expanders = array();
        $this->expanderInitializer = $expanderInitializer;
    }

    /**
     * @param $type
     * @return boolean
     */
    public function is($type): bool
    {
        return strtolower($this->type) === strtolower($type);
    }

    /**
     * @return string
     */
    public function getType(): ?string
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
    public function matchExpanders($value): bool
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
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function hasExpander(string $expanderName): bool
    {
        foreach ($this->expanders as $expander) {
            if (!$this->expanderInitializer->hasExpanderDefinition($expanderName)) {
                continue;
            }

            if ($this->expanderInitializer->getExpanderDefinition($expanderName) === get_class($expander)) {
                return true;
            }
        }

        return false;
    }
}
