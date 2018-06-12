<?php
declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\ModifiableMatcher;
use Coduo\PHPMatcher\Matcher\Modifier\MatcherModifier;
use Coduo\PHPMatcher\Parser\ModifiersRegistry;

final class InterceptingModifiableMatcher extends ModifiableMatcher
{
    /** @var MatcherModifier[] */
    private $applied = [];

    public function supportedModifiers(): array
    {
        return \array_keys(ModifiersRegistry::BUILT_IN_MODIFIERS);
    }

    public function getMatchers(): array
    {
        return [];
    }

    public function applyModifier(MatcherModifier $modifier)
    {
        $this->applied[] = $modifier;
    }

    public function canMatch($pattern): bool
    {
        return true;
    }

    public function appliedModifiers(): array
    {
        return $this->applied;
    }
}
