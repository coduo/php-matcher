<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Exception\Exception;
use Coduo\PHPMatcher\Matcher\Modifier\CaseInsensitive;
use Coduo\PHPMatcher\Matcher\Modifier\IgnoreExtraKeys;
use Coduo\PHPMatcher\Matcher\Modifier\MatcherModifier;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

final class ArrayMatcher extends ModifiableMatcher
{
    const PATTERN = 'array';
    const UNBOUNDED_PATTERN = '@...@';

    const SUPPORTED_MODIFIERS = [
        IgnoreExtraKeys::NAME,
        CaseInsensitive::NAME
    ];

    private $propertyMatcher;

    private $accessor;

    private $parser;

    /**
     * @var bool
     */
    private $ignoreExtraKeys;

    public function __construct(ValueMatcher $propertyMatcher, Parser $parser)
    {
        $this->propertyMatcher = $propertyMatcher;
        $this->parser = $parser;

        $this->ignoreExtraKeys = false;
    }

    public function match($value, $pattern) : bool
    {
        if (parent::match($value, $pattern)) {
            return true;
        }

        if (!\is_array($value)) {
            $this->error = \sprintf('%s "%s" is not a valid array.', \gettype($value), new StringConverter($value));
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

    public function canMatch($pattern) : bool
    {
        return \is_array($pattern) || $this->isArrayPattern($pattern);
    }

    public function supportedModifiers(): array
    {
        return self::SUPPORTED_MODIFIERS;
    }

    public function getMatchers(): array
    {
        return [$this->propertyMatcher];
    }

    public function applyModifier(MatcherModifier $modifier)
    {
        switch ($modifier->getName()) {
            case IgnoreExtraKeys::NAME:
                $this->ignoreExtraKeys = true;
                break;
        }
    }

    private function isArrayPattern($pattern) : bool
    {
        if (!\is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parseTypePattern($pattern)->is(self::PATTERN);
    }

    private function iterateMatch(array $values, array $patterns, string $parentPath = '') : bool
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

            if (!\is_array($value) || !$this->canMatch($pattern)) {
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

    private function isPatternValid(array $pattern, array $values, string $parentPath) : bool
    {
        if (\is_array($pattern)) {
            $skipPattern = static::UNBOUNDED_PATTERN;

            $pattern = \array_filter(
                $pattern,
                function ($item) use ($skipPattern) {
                    return $item !== $skipPattern;
                }
            );

            $notExistingKeys = $this->findNotExistingKeys($pattern, $values);
            if (\count($notExistingKeys) > 0 && !$this->ignoreExtraKeys) {
                $keyNames = \array_keys($notExistingKeys);
                $path = $this->formatFullPath($parentPath, $this->formatAccessPath($keyNames[0]));
                $this->setMissingElementInError('value', $path);

                return false;
            }
        }

        return true;
    }

    private function findNotExistingKeys(array $pattern, array $values) : array
    {
        $notExistingKeys = \array_diff_key($pattern, $values);

        return \array_filter($notExistingKeys, function ($pattern) use ($values) {
            if (\is_array($pattern)) {
                return !$this->match($values, $pattern);
            }

            try {
                $typePattern = $this->parser->parseTypePattern($pattern);
            } catch (Exception $e) {
                return true;
            } catch (\Throwable $t) {
                return true;
            }

            return !$typePattern->hasExpander('optional');
        });
    }

    private function valueMatchPattern($value, $pattern) : bool
    {
        $match = $this->propertyMatcher->canMatch($pattern) &&
            true === $this->propertyMatcher->match($value, $pattern);

        if (!$match) {
            $this->error = $this->propertyMatcher->getError();
        }

        return $match;
    }

    private function valueExist(string $path, array $haystack) : bool
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


    private function arrayPropertyExists(string $property, array $objectOrArray) : bool
    {
        return ($objectOrArray instanceof \ArrayAccess && isset($objectOrArray[$property])) ||
            (\is_array($objectOrArray) && \array_key_exists($property, $objectOrArray));
    }

    private function getValueByPath(array $array, string $path)
    {
        return $this->getPropertyAccessor()->getValue($array, $path);
    }

    private function getPropertyAccessor() : PropertyAccessorInterface
    {
        if (isset($this->accessor)) {
            return $this->accessor;
        }

        $accessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $this->accessor = $accessorBuilder->getPropertyAccessor();

        return $this->accessor;
    }

    private function setMissingElementInError(string $place, string $path)
    {
        $this->error = \sprintf('There is no element under path %s in %s.', $path, $place);
    }

    private function formatAccessPath($key) : string
    {
        return \sprintf('[%s]', $key);
    }

    private function formatFullPath(string $parentPath, string $path) : string
    {
        return \sprintf('%s%s', $parentPath, $path);
    }

    private function shouldSkippValueMatchingFor($lastPattern) : bool
    {
        return $lastPattern === self::UNBOUNDED_PATTERN;
    }

    private function allExpandersMatch($value, $pattern) : bool
    {
        $typePattern = $this->parser->parseTypePattern($pattern);
        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            return false;
        }

        return true;
    }
}
