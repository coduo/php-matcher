<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class HasProperty implements PatternExpander
{
    public const NAME = 'hasProperty';

    use BacktraceBehavior;

    private $propertyName;
    private $error;

    public function __construct($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    public static function is(string $name): bool
    {
        return self::NAME === $name;
    }

    public function match($value): bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (\is_array($value)) {
            $hasProperty = \array_key_exists($this->propertyName, $value);

            if (!$hasProperty) {
                $this->error = \sprintf('"json" object "%s" does not have "%s" propety.', new StringConverter($value), new StringConverter($this->propertyName));
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

                return false;
            }

            $this->backtrace->expanderSucceed(self::NAME, $value);

            return true;
        }

        if (!Json::isValid($value)) {
            $this->error = \sprintf('HasProperty expander require valid "json" string, got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $jsonArray = \json_decode(Json::reformat($value), true);

        $hasProperty = \array_key_exists($this->propertyName, $jsonArray);

        if (!$hasProperty) {
            $this->error = \sprintf('"json" object "%s" does not have "%s" propety.', new StringConverter($value), new StringConverter($this->propertyName));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
