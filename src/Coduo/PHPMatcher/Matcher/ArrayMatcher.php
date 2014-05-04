<?php

namespace Coduo\PHPMatcher\Matcher;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ArrayMatcher extends Matcher
{
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
     * @return bool
     */
    private function iterateMatch(array $value, array $pattern)
    {
        foreach ($value as $key => $element) {
            $path = sprintf("[%s]", $key);

            if (!$this->hasValue($pattern, $path)) {
                $this->error = sprintf('There is no element under path %s in pattern array.', $path);
                return false;
            }
            $elementPattern = $this->getValue($pattern, $path);
            if ($this->propertyMatcher->canMatch($elementPattern)) {
                if (true === $this->propertyMatcher->match($element, $elementPattern)) {
                    continue;
                }
            }

            if (!is_array($element)) {
                $this->error = $this->propertyMatcher->getError();
                return false;
            }

            if (false === $this->iterateMatch($element, $elementPattern)) {
                return false;
            }
        }
    }

    /**
     * @param $array
     * @param $path
     * @return bool
     */
    private function hasValue($array, $path)
    {
        try {
            $this->getPropertyAccessor()->getValue($array, $path);
        } catch (NoSuchIndexException $e) {
            return false;
        }

        return true;
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
        $accessorBuilder->enableExceptionOnInvalidIndex();
        $this->accessor = $accessorBuilder->getPropertyAccessor();

        return $this->accessor;
    }
}
