<?php
declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Modifier;

final class CaseInsensitive implements MatcherModifier
{
    const NAME = 'case_insensitive';

    public function getName(): string
    {
        return self::NAME;
    }
}
