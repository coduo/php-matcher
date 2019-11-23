<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\HasProperty;
use PHPUnit\Framework\TestCase;

class HasPropertyTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($propertyName, $value, $expectedResult)
    {
        $expander = new HasProperty($propertyName);
        $expander->setBacktrace(new Backtrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return [
            ['property','{"property":1}',true],
            ['property','{"property_02":1}',false],
            ['property',['property' => 1],true],
            ['property',['property_02' => 1],false],
            ['property','{"object_nested":{"property": 1}}',false],
        ];
    }
}
