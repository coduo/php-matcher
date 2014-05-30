<?php

namespace Coduo\PHPMatcher\Matcher;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ArrayMatcher extends Matcher
{
    const UNBOUNDED_PATTERN = '@...@';

    /**
     * @var PropertyMatcher
     */
    private $propertyMatcher;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    public function __construct(PropertyMatcher $propertyMatcher)
    {
        $this->propertyMatcher = $propertyMatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_array($value)) {
            return false;
        }

        if (false === $this->iterateMatch($value, $pattern)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_array($pattern);
    }

    /**
     * @param  array $value
     * @param  array $pattern
     * @param string $parentPath
     * @return bool
     */
    private function iterateMatch(array $value, array $pattern, $parentPath = "")
    {
        $lastPattern = array_values($pattern);
        $unboundedMode = end($lastPattern) === self::UNBOUNDED_PATTERN;

        if ($unboundedMode) {
            $unboundedPattern = prev($lastPattern);
            array_pop($pattern);
        }

        foreach ($value as $key => $element) {
            $path = sprintf("[%s]", $key);

            if ($this->hasValue($pattern, $path)) {
                $elementPattern = $this->getValue($pattern, $path);
            } else if ($unboundedMode) {
                $elementPattern = $unboundedPattern;
            } else {
                $this->error = sprintf('There is no element under path %s%s in pattern.', $parentPath, $path);
                return false;
            }

            if ($this->propertyMatcher->canMatch($elementPattern)) {
                if (true === $this->propertyMatcher->match($element, $elementPattern)) {
                    continue;
                }
            }

            if (!is_array($element) || !is_array($elementPattern)) {
                $this->error = $this->propertyMatcher->getError();
                return false;
            }

            if (false === $this->iterateMatch($element, $elementPattern, $parentPath . $path)) {
                return false;
            }
        }

        return $this->checkIfPathsFromPatternExistInValue($value, $pattern, $parentPath);
    }

    /**
     * @param $array
     * @param $path
     * @return bool
     */
    private function hasValue($array, $path)
    {
        return null !== $this->getPropertyAccessor()->getValue($array, $path);
    }

    /**
     * @param $array
     * @param $path
     * @return mixed
     */
    private function getValue($array, $path)
    {
        return $this->getPropertyAccessor()->getValue($array, $path);
    }

    /**
     * @return \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private function getPropertyAccessor()
    {
        if (isset($this->accessor)) {
            return $this->accessor;
        }

        $accessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $this->accessor = $accessorBuilder->getPropertyAccessor();

        return $this->accessor;
    }

    /**
     * @param array $value
     * @param array $pattern
     * @param $parentPath
     * @return bool
     */
    private function checkIfPathsFromPatternExistInValue(array $value, array $pattern, $parentPath)
    {
        if (is_array($pattern)) {
            $notExistingKeys = array_diff_key($pattern, $value);

            if (count($notExistingKeys) > 0) {
                $keyNames = array_keys($notExistingKeys);
                $this->error = sprintf('There is no element under path %s[%s] in value.', $parentPath, $keyNames[0]);
                return false;
            }
        }

        return true;
    }
}
