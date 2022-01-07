<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Backtrace\VoidBacktrace;
use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\Json;
use SebastianBergmann\Comparator\ComparisonFailure;

final class PHPMatcherConstraint extends Constraint
{
    private $pattern;

    private PHPMatcher $matcher;

    public function __construct($pattern, Backtrace $backtrace = null)
    {
        if (!\in_array(\gettype($pattern), ['string', 'array', 'object'], true)) {
            throw new \LogicException(\sprintf('The PHPMatcherConstraint pattern must be a string, closure or an array, %s given.', \gettype($pattern)));
        }

        if (\is_object($pattern) && !\is_callable($pattern)) {
            throw new \LogicException(\sprintf('The PHPMatcherConstraint pattern must be a string, closure or an array, %s given.', \gettype($pattern)));
        }

        $this->pattern = $pattern;
        $this->matcher = new PHPMatcher($backtrace);
    }

    /**
     * {@inheritdoc}
     */
    public function toString() : string
    {
        return 'matches given pattern.';
    }

    protected function failureDescription($other) : string
    {
        $errorDescription = $this->matcher->error() ?: 'Value does not match given pattern';
        $backtrace = $this->matcher->backtrace();

        return $backtrace instanceof VoidBacktrace
            ? $errorDescription
            : $errorDescription
                . "\nBacktrace:\n" . $this->matcher->backtrace();
    }

    protected function matches($other) : bool
    {
        return $this->matcher->match($other, $this->pattern);
    }

    /**
     * {@inheritdoc}
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null) : void
    {
        parent::fail($other, $description, $comparisonFailure ?? $this->createComparisonFailure($other));
    }

    private function createComparisonFailure($other) : ?ComparisonFailure
    {
        if (!\is_string($other) || !\is_string($this->pattern) || !\class_exists(Json::class)) {
            return null;
        }

        [$error, $otherJson] = Json::canonicalize($other);

        if ($error) {
            return null;
        }

        [$error, $patternJson] = Json::canonicalize($this->pattern);

        if ($error) {
            return null;
        }

        return new ComparisonFailure(
            \json_decode($this->pattern),
            \json_decode($other),
            Json::prettify($patternJson),
            Json::prettify($otherJson),
            false,
            'Failed asserting that the pattern matches the given value.'
        );
    }
}
