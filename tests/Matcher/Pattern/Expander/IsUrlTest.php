<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsUrl;
use PHPUnit\Framework\TestCase;

class IsUrlTest extends TestCase
{
    public static function examplesUrlsProvider()
    {
        return [
            ['http://example.com/test.html', true],
            ['https://example.com/test.html', true],
            ['https://example.com/user/{id}/', true],
            ['mailto:email@example.com', true],
            ['//example.com/test/', false],
            ['example', false],
            ['', false],
        ];
    }

    /**
     * @dataProvider examplesUrlsProvider
     */
    public function test_urls($url, $expectedResult) : void
    {
        $expander = new IsUrl();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($url));
    }
}
