<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Modifier\CaseInsensitive;
use Coduo\PHPMatcher\Matcher\Modifier\MatcherModifier;
use Coduo\ToString\StringConverter;

final class ScalarMatcher extends ModifiableMatcher
{
    const SUPPORTED_MODIFIERS = [
        CaseInsensitive::NAME
    ];

    /** @var bool */
    private $caseInsensitive;

    public function __construct()
    {
        $this->caseInsensitive = false;
    }

    public function match($value, $pattern) : bool
    {
        if (!$this->caseInsensitive && $value !== $pattern) {
            $this->setError('does not match', $value, $pattern);
            return false;
        }

        if (
            $this->caseInsensitive
            && \is_string($value)
            && \is_string($pattern)
            && \strcasecmp($value, $pattern) !== 0
        ) {
            $this->setError('does not case-insensitive match', $value, $pattern);
            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        return \is_scalar($pattern);
    }

    public function supportedModifiers(): array
    {
        return self::SUPPORTED_MODIFIERS;
    }

    public function getMatchers(): array
    {
        return [];
    }

    public function applyModifier(MatcherModifier $modifier)
    {
        switch ($modifier->getName()) {
            case CaseInsensitive::NAME:
                $this->caseInsensitive= true;
                break;
        }
    }

    /**
     * @return void
     */
    private function setError(string $message, $value, $pattern)
    {
        $this->error = \sprintf('"%s" ' . $message . ' "%s".', new StringConverter($value), new StringConverter($pattern));
    }
}
