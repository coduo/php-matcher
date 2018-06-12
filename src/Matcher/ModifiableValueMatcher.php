<?php
declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Modifier\MatcherModifier;

interface ModifiableValueMatcher extends ValueMatcher
{
    public function supportsModifier(MatcherModifier $modifier): bool;

    /**
     * @return void
     */
    public function modify(MatcherModifier $modifier);
}
