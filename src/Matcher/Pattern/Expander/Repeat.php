<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Factory\MatcherFactory;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Repeat implements PatternExpander
{
    const NAME = 'repeat';

    private $error;

    private $pattern;

    private $isStrict;

    private $isScalar;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    /**
     * @param array|string $pattern array to be matched or json-encoded string
     */
    public function __construct($pattern, bool $isStrict = true)
    {
        $this->pattern = $pattern;
        $this->isStrict = $isStrict;
        $this->isScalar = true;

        if (\is_string($pattern)) {
            $json = \json_decode($pattern, true);
            if ($json !== null && \json_last_error() === JSON_ERROR_NONE) {
                $this->pattern = $json;
                $this->isScalar = false;
            }
        } elseif (\is_array($pattern)) {
            $this->isScalar = false;
        } else {
            throw new \InvalidArgumentException('Repeat pattern must be a string or an array.');
        }
    }

    public function match($values) : bool
    {
        if (!\is_array($values)) {
            $this->error = \sprintf('Repeat expander require "array", got "%s".', new StringConverter($values));
            return false;
        }

        $factory = new MatcherFactory();
        $matcher = $factory->createMatcher();

        if ($this->isScalar) {
            return $this->matchScalar($values, $matcher);
        }

        return $this->matchJson($values, $matcher);
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    private function matchScalar(array $values, Matcher $matcher) : bool
    {
        foreach ($values as $index => $value) {
            $match = $matcher->match($value, $this->pattern);

            if (!$match) {
                $this->error = \sprintf('Repeat expander, entry n°%d, find error : %s', $index, $matcher->getError());
                return false;
            }
        }

        return true;
    }

    private function matchJson(array $values, Matcher $matcher) : bool
    {
        $patternKeys = \array_keys($this->pattern);
        $patternKeysLength = \count($patternKeys);

        foreach ($values as $index => $value) {
            $valueKeys = \array_keys($value);
            $valueKeysLength = \count($valueKeys);

            if ($this->isStrict && $patternKeysLength !== $valueKeysLength) {
                $this->error = \sprintf('Repeat expander expect to have %d keys in array but get : %d', $patternKeysLength, $valueKeysLength);
                return false;
            }

            foreach ($patternKeys as $key) {
                if (!\array_key_exists($key, $value)) {
                    $this->error = \sprintf('Repeat expander, entry n°%d, require "array" to have key "%s".', $index, $key);
                    return false;
                }

                $match = $matcher->match($value[$key], $this->pattern[$key]);

                if (!$match) {
                    $this->error = \sprintf('Repeat expander, entry n°%d, key "%s", find error : %s', $index, $key, $matcher->getError());
                    return false;
                }
            }
        }

        return true;
    }
}
