<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

final class ArrayMatcher extends Matcher
{
    const UNBOUNDED_PATTERN = '@...@';

    /**
     * @var ValueMatcher
     */
    private $propertyMatcher;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param ValueMatcher $propertyMatcher
     */
    public function __construct(ValueMatcher $propertyMatcher, Parser $parser)
    {
        $this->propertyMatcher = $propertyMatcher;
        $this->parser = $parser;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (parent::match($value, $pattern)) {
            return true;
        }

        if (!is_array($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid array.", gettype($value), new StringConverter($value));
            return false;
        }

        if ($this->isArrayPattern($pattern)) {
            return $this->allExpandersMatch($value, $pattern);
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
        return is_array($pattern) || $this->isArrayPattern($pattern);
    }

    private function isArrayPattern($pattern)
    {
        if (!is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is('array');
    }

    /**
     * @param  array $values
     * @param  array $patterns
     * @param string $parentPath
     * @return bool
     */
    private function iterateMatch(array $values, array $patterns, $parentPath = "")
    {
        $pattern = null;
        foreach ($values as $key => $value) {
            $path = $this->formatAccessPath($key);

            if ($this->shouldSkippValueMatchingFor($pattern)) {
                continue;
            }

            if ($this->valueExist($path, $patterns)) {
                $pattern = $this->getValueByPath($patterns, $path);
            } else {
                $this->setMissingElementInError('pattern', $this->formatFullPath($parentPath, $path));
                return false;
            }

            if ($this->shouldSkippValueMatchingFor($pattern)) {
                continue;
            }

            if ($this->valueMatchPattern($value, $pattern)) {
                continue;
            }

            if (!is_array($value) || !$this->canMatch($pattern)) {
                return false;
            }

            if ($this->isArrayPattern($pattern)) {
                if (!$this->allExpandersMatch($value, $pattern)) {
                    return false;
                }

                continue;
            }

            if (false === $this->iterateMatch($value, $pattern, $this->formatFullPath($parentPath, $path))) {
                return false;
            }
        }

        if (!$this->isPatternValid($patterns, $values, $parentPath)) {
            return false;
        }

        return true;
    }

    /**
     * Check if pattern elements exist in value array
     *
     * @param array $pattern
     * @param array $values
     * @param $parentPath
     * @return bool
     */
    private function isPatternValid(array $pattern, array $values, $parentPath)
    {
        if (is_array($pattern)) {
            $notExistingKeys = array_diff_key($pattern, $values);

            if (count($notExistingKeys) > 0) {
                $keyNames = array_keys($notExistingKeys);
                $path = $this->formatFullPath($parentPath, $this->formatAccessPath($keyNames[0]));
                $this->setMissingElementInError('value', $path);
                return false;
            }
        }

        return true;
    }

    /**
     * @param $value
     * @param $pattern
     * @return bool
     */
    private function valueMatchPattern($value, $pattern)
    {
        $match = $this->propertyMatcher->canMatch($pattern) &&
            true === $this->propertyMatcher->match($value, $pattern);

        if (!$match) {
            $this->error = $this->propertyMatcher->getError();
        }

        return $match;
    }

    /**
     * @param $path
     * @param $haystack
     * @return bool
     */
    private function valueExist($path, array $haystack)
    {
        $propertyPath = new PropertyPath($path);
        $length = $propertyPath->getLength();
        $valueExist = true;
        for ($i = 0; $i < $length; ++$i) {
            $property = $propertyPath->getElement($i);
            $isIndex = $propertyPath->isIndex($i);
            $propertyExist = $this->arrayPropertyExists($property, $haystack);

            if ($isIndex && !$propertyExist) {
                $valueExist = false;
                break;
            }
        }

        unset($propertyPath);
        return $valueExist;
    }

    /**
     * @param string $property
     * @param array $objectOrArray
     * @return bool
     */
    private function arrayPropertyExists($property, array $objectOrArray)
    {
        return ($objectOrArray instanceof \ArrayAccess && isset($objectOrArray[$property])) ||
            (is_array($objectOrArray) && array_key_exists($property, $objectOrArray));
    }

    /**
     * @param $array
     * @param $path
     * @return mixed
     */
    private function getValueByPath($array, $path)
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
     * @param $place
     * @param $path
     */
    private function setMissingElementInError($place, $path)
    {
        $this->error = sprintf('There is no element under path %s in %s.', $path, $place);
    }

    /**
     * @param $key
     * @return string
     */
    private function formatAccessPath($key)
    {
        return sprintf("[%s]", $key);
    }

    /**
     * @param $parentPath
     * @param $path
     * @return string
     */
    private function formatFullPath($parentPath, $path)
    {
        return sprintf("%s%s", $parentPath, $path);
    }

    /**
     * @param $lastPattern
     * @return bool
     */
    private function shouldSkippValueMatchingFor($lastPattern)
    {
        return $lastPattern === self::UNBOUNDED_PATTERN;
    }

    /**
     * @param $value
     * @param $pattern
     * @return bool
     * @throws \Coduo\PHPMatcher\Exception\UnknownExpanderException
     */
    private function allExpandersMatch($value, $pattern)
    {
        $typePattern = $this->parser->parse($pattern);
        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            return false;
        }

        return true;
    }
}
