<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern;

use Coduo\PHPMatcher\Matcher\Pattern\TypePattern;
use Coduo\PHPMatcher\Matcher\Pattern\RegexConverter;
use PHPUnit\Framework\TestCase;

class RegexConverterTest extends TestCase
{
    /**
     * @var RegexConverter
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new RegexConverter();
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\UnknownTypeException
     */
    public function test_convert_unknown_type()
    {
        $this->converter->toRegex(new TypePattern('not_a_type'));
    }
}
