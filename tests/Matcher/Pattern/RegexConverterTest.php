<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern;

use Coduo\PHPMatcher\Matcher\Pattern\RegexConverter;
use Coduo\PHPMatcher\Matcher\Pattern\TypePattern;
use PHPUnit\Framework\TestCase;

class RegexConverterTest extends TestCase
{
    private ?RegexConverter $converter = null;

    public function setUp() : void
    {
        $this->converter = new RegexConverter();
    }

    public function test_convert_unknown_type() : void
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\UnknownTypeException::class);

        $this->converter->toRegex(new TypePattern('not_a_type'));
    }
}
