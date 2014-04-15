<?php

namespace JsonMatcher\Matcher;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;

class ArrayMatcher implements PropertyMatcher
{
    /**
     * @var PropertyMatcher
     */
    private $propertyMatcher;

    private $paths;

    public function __construct(PropertyMatcher $propertyMatcher)
    {
        $this->propertyMatcher = $propertyMatcher;
    }

    public function match($value, $pattern)
    {
        $accessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $accessorBuilder->enableExceptionOnInvalidIndex();
        $accessor = $accessorBuilder->getPropertyAccessor();

        $this->paths = array();
        foreach ($value as $key => $element) {
            $path = sprintf("[%s]", $key);

            if (is_array($element)) {
                $this->buildPath($element, $path);
                continue;
            }

            $this->paths[] = $path;
        }

        foreach ($this->paths as $path) {
            $elementValue = $accessor->getValue($value, $path);
            try {
                $patternValue = $accessor->getValue($pattern, $path);
            } catch (NoSuchIndexException $e) {
                return false;
            }

            if ($this->propertyMatcher->canMatch($patternValue)) {
                if (false === $this->propertyMatcher->match($elementValue, $patternValue)) {
                    return false;
                }
            }

        }

        return true;
    }

    public function canMatch($pattern)
    {
        return is_array($pattern);
    }

    private function buildPath(array $array, $parentPath)
    {
        foreach ($array as $key => $element) {
            $path = sprintf("%s[%s]", $parentPath, $key);

            if (is_array($element)) {
                $this->buildPath($element, $path);
                continue;
            }

            $this->paths[] = $path;
        }
    }
}
