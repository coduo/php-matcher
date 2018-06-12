<?php
declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Modifier\MatcherModifier;

abstract class ModifiableMatcher implements ModifiableValueMatcher
{
    protected $error;

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function match($value, $pattern) : bool
    {
        if ($value === $pattern) {
            return true;
        }

        return false;
    }

    public function supportsModifier(MatcherModifier $modifier): bool
    {
        return \in_array($modifier->getName(), $this->supportedModifiers(), true);
    }

    public function modify(MatcherModifier $modifier)
    {
        foreach ($this->getMatchers() as $matcher) {
            if ($matcher instanceof ModifiableValueMatcher && $matcher->supportsModifier($modifier)) {
                $matcher->modify($modifier);
            }
        }
        $this->applyModifier($modifier);
    }

    abstract public function supportedModifiers(): array;

    /**
     * @return ValueMatcher[]
     */
    abstract public function getMatchers(): array;

    /**
     * @return void
     */
    abstract public function applyModifier(MatcherModifier $modifier);
}
