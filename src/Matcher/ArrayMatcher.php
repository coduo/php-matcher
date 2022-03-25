<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Exception\Exception;
use Coduo\PHPMatcher\Matcher\ArrayMatcher\Diff;
use Coduo\PHPMatcher\Matcher\ArrayMatcher\Difference;
use Coduo\PHPMatcher\Matcher\ArrayMatcher\StringDifference;
use Coduo\PHPMatcher\Matcher\ArrayMatcher\ValuePatternDifference;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class ArrayMatcher extends Matcher
{
    /**
     * @var string
     */
    public const PATTERN = 'array';

    /**
     * @var string
     */
    public const UNBOUNDED_PATTERN = '@...@';

    /**
     * @var string
     */
    public const UNIVERSAL_KEY = '@*@';

    public const ARRAY_PREVIOUS_PATTERN = '@array_previous@';

    public const ARRAY_PREVIOUS_PATTERN_REPEAT = '@array_previous_repeat@';

    private ValueMatcher $propertyMatcher;

    private Parser $parser;

    private Backtrace $backtrace;

    private Diff $diff;

    public function __construct(ValueMatcher $propertyMatcher, Backtrace $backtrace, Parser $parser)
    {
        $this->diff = new Diff();
        $this->propertyMatcher = $propertyMatcher;
        $this->parser = $parser;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (parent::match($value, $pattern)) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);

            return true;
        }

        if (!\is_array($value)) {
            $this->addValuePatternDifference($value, $pattern);

            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->getError());

            return false;
        }

        if ($this->isArrayPattern($pattern)) {
            return $this->allExpandersMatch($value, $pattern);
        }

        if (!$this->iterateMatch($value, $pattern)) {
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->getError());

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        return \is_array($pattern) || $this->isArrayPattern($pattern);
    }

    public function getError() : ?string
    {
        if (!$this->diff->count()) {
            return null;
        }

        return \implode("\n", \array_map(fn (Difference $difference) : string => $difference->format(), $this->diff->all()));
    }

    public function clearError() : void
    {
        $this->diff = new Diff();
    }

    private function isArrayPattern($pattern) : bool
    {
        if (!\is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::PATTERN);
    }

    private function iterateMatch(array $values, array $patterns, string $parentPath = '') : bool
    {
        $pattern = null;
        $previousPattern = null;

        /** @psalm-suppress InvalidArrayOffset */
        if (\in_array(self::ARRAY_PREVIOUS_PATTERN_REPEAT, $patterns, true)) {
            $patterns = \array_merge(
                \array_replace($patterns, [\array_search(self::ARRAY_PREVIOUS_PATTERN_REPEAT, $patterns, true) => self::ARRAY_PREVIOUS_PATTERN]),
                \array_fill(0, \count($values) - \count($patterns), self::ARRAY_PREVIOUS_PATTERN)
            );
        }

        foreach ($values as $key => $value) {
            $path = $this->formatAccessPath($key);

            if ($this->shouldSkipValueMatchingFor($pattern)) {
                continue;
            }

            if ($this->valueExist($path, $patterns)) {
                $pattern = $this->getValueByPath($patterns, $path);
            } elseif (isset($patterns[self::UNIVERSAL_KEY])) {
                $pattern = $patterns[self::UNIVERSAL_KEY];
            } else {
                $this->setMissingElementInError('pattern', $this->formatFullPath($parentPath, $path));

                return false;
            }

            if ($pattern === self::ARRAY_PREVIOUS_PATTERN) {
                $pattern = $previousPattern;
            }

            if ($this->shouldSkipValueMatchingFor($pattern)) {
                continue;
            }

            if ($this->valueMatchPattern($value, $pattern, $this->formatFullPath($parentPath, $path))) {
                continue;
            }

            if (!\is_array($value)) {
                return false;
            }

            if (!$this->canMatch($pattern)) {
                $this->addValuePatternDifference($value, $parentPath, $this->formatFullPath($parentPath, $path));

                return false;
            }

            if ($this->isArrayPattern($pattern)) {
                if (!$this->allExpandersMatch($value, $pattern, $parentPath)) {
                    $this->addValuePatternDifference($value, $parentPath, $this->formatFullPath($parentPath, $path));

                    return false;
                }

                continue;
            }

            if (!$this->iterateMatch($value, $pattern, $this->formatFullPath($parentPath, $path))) {
                return false;
            }

            $previousPattern = $pattern;
        }

        return $this->isPatternValid($patterns, $values, $parentPath);
    }

    private function isPatternValid(array $pattern, array $values, string $parentPath) : bool
    {
        $skipPattern = self::UNBOUNDED_PATTERN;

        $pattern = \array_filter(
            $pattern,
            fn ($item) => $item !== $skipPattern
        );

        $notExistingKeys = $this->findNotExistingKeys($pattern, $values);

        if (\count($notExistingKeys) > 0) {
            $keyNames = \array_keys($notExistingKeys);
            $path = $this->formatFullPath($parentPath, $this->formatAccessPath($keyNames[0]));
            $this->setMissingElementInError('value', $path);

            return false;
        }

        return true;
    }

    /**
     * @return mixed[]
     */
    private function findNotExistingKeys(array $patterns, array $values) : array
    {
        $notExistingKeys = \array_diff_key($patterns, $values);

        return \array_filter($notExistingKeys, function ($pattern, $key) use ($values) : bool {
            if ($key === self::UNIVERSAL_KEY) {
                return false;
            }

            if (\is_array($pattern)) {
                return empty($pattern) || !$this->match($values, $pattern);
            }

            try {
                $typePattern = $this->parser->parse($pattern);
            } catch (Exception | \Throwable $e) {
                return true;
            }

            return !$typePattern->hasExpander('optional');
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function valueMatchPattern($value, $pattern, $parentPath) : bool
    {
        $match = $this->propertyMatcher->canMatch($pattern) &&
            $this->propertyMatcher->match($value, $pattern);

        if (!$match) {
            if (!\is_array($value)) {
                $this->addValuePatternDifference($value, $pattern, $parentPath);
            }
        }

        return $match;
    }

    private function valueExist(string $path, array $haystack) : bool
    {
        return $this->arrayPropertyExists($this->getKeyFromAccessPath($path), $haystack);
    }

    private function arrayPropertyExists(string $property, array $objectOrArray) : bool
    {
        return isset($objectOrArray[$property]) ||
            \array_key_exists($property, $objectOrArray);
    }

    private function getValueByPath(array $array, string $path)
    {
        return $array[$this->getKeyFromAccessPath($path)];
    }

    private function setMissingElementInError(string $place, string $path) : void
    {
        $this->diff = $this->diff->add(new StringDifference(\sprintf('There is no element under path %s in %s.', $path, $place)));
    }

    private function formatAccessPath($key) : string
    {
        return \sprintf('[%s]', $key);
    }

    private function getKeyFromAccessPath(string $path) : string
    {
        return \substr($path, 1, -1);
    }

    private function formatFullPath(string $parentPath, string $path) : string
    {
        return \sprintf('%s%s', $parentPath, $path);
    }

    private function shouldSkipValueMatchingFor($lastPattern) : bool
    {
        return $lastPattern === self::UNBOUNDED_PATTERN;
    }

    private function allExpandersMatch($value, $pattern, $parentPath = '') : bool
    {
        $typePattern = $this->parser->parse($pattern);

        if (!$typePattern->matchExpanders($value)) {
            $this->addValuePatternDifference($value, $pattern, $parentPath);

            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->getError());

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    private function addValuePatternDifference($value, $pattern, string $path = '') : void
    {
        $this->diff = $this->diff->add(new ValuePatternDifference(
            (string) new StringConverter($value),
            (string) new StringConverter($pattern),
            $path ? $path : 'root'
        ));
    }
}
