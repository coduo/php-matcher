<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\Json;
use SebastianBergmann\Comparator\ComparisonFailure;

final class PHPMatcherConstraint extends Constraint
{
    private $pattern;
    private $matcher;
    private $lastValue;

    public function __construct($pattern)
    {
        if (!\in_array(\gettype($pattern), ['string', 'array'])) {
            throw new \LogicException(\sprintf('The PHPMatcherConstraint pattern must be a string or an array, %s given.', \gettype($pattern)));
        }
        if (\method_exists(Constraint::class, '__construct')) {
            parent::__construct();
        }

        $this->pattern = $pattern;
        $this->matcher = $this->createMatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function toString() : string
    {
        return 'matches the pattern';
    }

    protected function additionalFailureDescription($other) : string
    {
        return $this->matcher->getError();
    }

    protected function matches($value) : bool
    {
        return $this->matcher->match($this->lastValue = $value, $this->pattern);
    }

    private function createMatcher() : Matcher
    {
        $factory = new SimpleFactory();

        return $factory->createMatcher();
    }

    /**
     * {@inheritdoc}
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null): void
    {
        if (null === $comparisonFailure
            && \is_string($other)
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
