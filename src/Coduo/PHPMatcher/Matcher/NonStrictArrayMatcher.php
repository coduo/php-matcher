<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Parser;

class NonStrictArrayMatcher extends ArrayMatcher
{
    /**
     * @param  array $values
     * @param  array $patterns
     * @param string $parentPath
     * @return bool
     */
    protected  function iterateMatch(array $values, array $patterns, $parentPath = "")
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
                continue;
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
}
