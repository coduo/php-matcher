<?php
declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Modifier;

final class IgnoreExtraKeys implements MatcherModifier
{
    const NAME = 'ignore_extra_keys';

    public function getName(): string
    {
        return self::NAME;
    }
}
