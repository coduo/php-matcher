<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Exception;

class UnknownModifierClassException extends \Exception
{
    public function __construct(string $name, string $class)
    {
        parent::__construct(\sprintf(
            'Unknown class %s for modifier %s',
            $class,
            $name
        ));
    }
}
