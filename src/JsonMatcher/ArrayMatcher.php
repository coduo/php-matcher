<?php

namespace JsonMatcher;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayMatcher
{
    /**
     * @var array
     */
    private $matcher;

    /**
     * @var array
     */
    private $paths;

    public function __construct(array $matcher)
    {
        $this->matcher = $matcher;
        $this->paths = array();
        foreach ($this->matcher as $key => $element) {
            $path = sprintf("[%s]", $key);

            if (is_array($element)) {
                $this->buildPath($element, $path);
                continue;
            }

            $this->paths[] = $path;
        }
    }

    public function match(array $pattern)
    {
        $accessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $accessorBuilder->enableExceptionOnInvalidIndex();
        $accessor = $accessorBuilder->getPropertyAccessor();

        foreach ($this->paths as $path) {
            $value = $accessor->getValue($this->matcher, $path);
            $patternValue = $accessor->getValue($pattern, $path);
        }

        return false;
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
