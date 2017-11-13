<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Repeat implements PatternExpander
{
    const NAME = 'repeat';

    /**
     * @var null|string
     */
    private $error;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var bool
     */
    private $isStrict;

    /**
     * @var bool
     */
    private $isScalar;

    /**
     * {@inheritdoc}
     */
    public static function is($name)
    {
        return self::NAME === $name;
    }

    /**
     * @param $value
     */
    public function __construct($pattern, $isStrict = true)
    {
        if (!is_string($pattern)) {
            throw new \InvalidArgumentException("Repeat pattern must be a string.");
        }

        $this->pattern = $pattern;
        $this->isStrict = $isStrict;
        $this->isScalar = true;

        $json = json_decode($pattern, true);

        if ($json !== null && json_last_error() === JSON_ERROR_NONE) {
            $this->pattern = $json;
            $this->isScalar = false;
        }
    }

    /**
     * @param $values
     * @return bool
     */
    public function match($values)
    {
        if (!is_array($values)) {
            $this->error = sprintf("Repeat expander require \"array\", got \"%s\".", new StringConverter($values));
            return false;
        }

        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();

        if ($this->isScalar) {
            return $this->matchScalar($values, $matcher);
        }

        return $this->matchJson($values, $matcher);
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param array $values
     * @param Matcher $matcher
     * @return bool
     */
    private function matchScalar(array $values, Matcher $matcher)
    {
        foreach ($values as $index => $value) {
            $match = $matcher->match($value, $this->pattern);

            if (!$match) {
                $this->error = sprintf("Repeat expander, entry n°%d, find error : %s", $index, $matcher->getError());
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $values
     * @param Matcher $matcher
     * @return bool
     */
    private function matchJson(array $values, Matcher $matcher)
    {
        $patternKeys = array_keys($this->pattern);
        $patternKeysLength = count($patternKeys);

        foreach ($values as $index => $value) {
            $valueKeys = array_keys($value);
            $valueKeysLength = count($valueKeys);

            if ($this->isStrict && $patternKeysLength !== $valueKeysLength) {
                $this->error = sprintf("Repeat expander expect to have %d keys in array but get : %d", $patternKeysLength, $valueKeysLength);
                return false;
            }

            foreach ($patternKeys as $key) {
                if (!array_key_exists($key, $value)) {
                    $this->error = sprintf("Repeat expander, entry n°%d, require \"array\" to have key \"%s\".", $index, $key);
                    return false;
                }

                $match = $matcher->match($value[$key], $this->pattern[$key]);

                if (!$match) {
                    $this->error = sprintf("Repeat expander, entry n°%d, key \"%s\", find error : %s", $index, $key, $matcher->getError());
                    return false;
                }
            }
        }

        return true;
    }
}
