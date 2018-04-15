<?php
declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;

final class TestParserFactory
{
    public static function get(): Parser
    {
        return new Parser(new Lexer(), new Parser\ExpanderInitializer(), new Parser\ModifiersRegistry());
    }
}
