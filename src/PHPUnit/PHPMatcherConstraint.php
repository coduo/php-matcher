<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Factory\MatcherFactory;
use Coduo\PHPMatcher\Matcher;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\Json;
use SebastianBergmann\Comparator\ComparisonFailure;

final class PHPMatcherConstraint extends Constraint
{
    private $pattern;
    private $matcher;
    private $backtrace;
    private $lastValue;

    public function __construct($pattern)
    {
        if (!\in_array(\gettype($pattern), ['string', 'array', 'object'])) {
            throw new \LogicException(\sprintf('The PHPMatcherConstraint pattern must be a string, closure or an array, %s given.', \gettype($pattern)));
        }

        if (\is_object($pattern) && !\is_callable($pattern)) {
            throw new \LogicException(\sprintf('The PHPMatcherConstraint pattern must be a string, closure or an array, %s given.', \gettype($pattern)));
        }

        if (\method_exists(Constraint::class, '__construct')) {
            parent::__construct();
        }

        $this->pattern = $pattern;
        $this->backtrace = new Backtrace();
        $this->matcher = $this->createMatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function toString() : string
    {
        return 'matches the pattern';
    }

    protected function failureDescription($other): string
    {
        return parent::failureDescription($other) . ".\nError: " . $this->matcher->getError();
    }

    protected function additionalFailureDescription($other) : string
    {
        return  "Backtrace:\n" . (string) $this->backtrace;
    }

    protected function matches($value) : bool
    {
        return $this->matcher->match($this->lastValue = $value, $this->pattern);
    }

    private function createMatcher() : Matcher
    {
        return (new MatcherFactory())->createMatcher($this->backtrace);
    }

    /**
     * {@inheritdoc}
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null) : void
    {
        if (null === $comparisonFailure
            && \is_string($other)
            && \is_string($this->pattern)
            && \class_exists(Json::class)
        ) {
            list($error) = Json::canonicalize($other);

            if ($error) {
                parent::fail($other, $description);
            }

            list($error) = Json::canonicalize($this->pattern);

            if ($error) {
                parent::fail($other, $description);
            }

            $comparisonFailure = new ComparisonFailure(
                \json_decode($this->pattern),
                \json_decode($other),
                Json::prettify($this->pattern),
                Json::prettify($other),
                false,
                'Failed asserting that the pattern matches the given value.'
            );
        }

        parent::fail($other, $description, $comparisonFailure);
    }
}
