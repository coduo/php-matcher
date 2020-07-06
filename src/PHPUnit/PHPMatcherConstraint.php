<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\Json;
use SebastianBergmann\Comparator\ComparisonFailure;
use LogicException;
use function class_exists;
use function gettype;
use function in_array;
use function is_callable;
use function is_object;
use function is_string;
use function json_decode;
use function sprintf;

final class PHPMatcherConstraint extends Constraint
{
    private $pattern;

    /**
     * @var PHPMatcher
     */
    private $matcher;

    private $lastValue;

    public function __construct($pattern)
    {
        if (!in_array(gettype($pattern), ['string', 'array', 'object'])) {
            throw new LogicException(sprintf('The PHPMatcherConstraint pattern must be a string, closure or an array, %s given.', gettype($pattern)));
        }

        if (is_object($pattern) && !is_callable($pattern)) {
            throw new LogicException(sprintf('The PHPMatcherConstraint pattern must be a string, closure or an array, %s given.', gettype($pattern)));
        }

        $this->pattern = $pattern;
        $this->matcher = new PHPMatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function toString() : string
    {
        return 'matches given pattern.';
    }

    protected function failureDescription($other): string
    {
        return parent::failureDescription($other)
            . "\nPattern: " . $this->exporter()->export($this->pattern)
            . "\nError: " . $this->matcher->error()
            . "\nBacktrace: \n" . $this->matcher->backtrace();
    }

    protected function matches($value) : bool
    {
        return $this->matcher->match($this->lastValue = $value, $this->pattern);
    }

    /**
     * {@inheritdoc}
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null) : void
    {
        if (null === $comparisonFailure
            && is_string($other)
            && is_string($this->pattern)
            && class_exists(Json::class)
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
                json_decode($this->pattern),
                json_decode($other),
                Json::prettify($this->pattern),
                Json::prettify($other),
                false,
                'Failed asserting that the pattern matches the given value.'
            );
        }

        parent::fail($other, $description, $comparisonFailure);
    }
}
