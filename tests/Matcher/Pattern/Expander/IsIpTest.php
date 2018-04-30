<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsIp;
use PHPUnit\Framework\TestCase;

class IsIpTest extends TestCase
{
    /**
     * @dataProvider examplesIpProvider
     */
    public function test_ip($ip, $expected)
    {
        $expander = new IsIp();
        $this->assertEquals($expected, $expander->match($ip));
    }

    public static function examplesIpProvider()
    {
        return [
            ['127.0.0.1', true],
            ['255.255.255.255', true],
            ['2001:0db8:0000:42a1:0000:0000:ab1c:0001', true],
            ['999.999.999.999', false],
            ['127.127', false],
            ['foo:bar:42:42', false]
        ];
    }
}
