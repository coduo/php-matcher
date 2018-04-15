<?php
declare(strict_types=1);

namespace Coduo\PHPMatcher\Parser;

use Coduo\PHPMatcher\Exception\UnknownModifierClassException;
use Coduo\PHPMatcher\Exception\UnknownModifierException;
use Coduo\PHPMatcher\Matcher\Modifier;

final class ModifiersRegistry
{
    /**
     * @var array
     */
    private $modifiers = [];

    public const BUILT_IN_MODIFIERS = [
        Modifier\IgnoreExtraKeys::NAME => Modifier\IgnoreExtraKeys::class,
        Modifier\CaseInsensitive::NAME => Modifier\CaseInsensitive::class
    ];

    public function __construct()
    {
        $this->modifiers = self::BUILT_IN_MODIFIERS;
    }

    public function register(string $name, string $class): void
    {
        if (!\class_exists($class) || \is_a($class, Modifier\MatcherModifier::class, true)) {
            throw new UnknownModifierClassException($name, $class);
        }

        $this->modifiers[$name] = $class;
    }

    public function has(string $name) : bool
    {
        return \array_key_exists($name, $this->modifiers);
    }

    public function get(string $name) : Modifier\MatcherModifier
    {
        if (!$this->has($name)) {
            throw new UnknownModifierException($name);
        }

        return $this->initialize($name);
    }

    private function initialize(string $name): Modifier\MatcherModifier
    {
        $reflection = new \ReflectionClass($this->modifiers[$name]);

        return $reflection->newInstance();
    }
}
