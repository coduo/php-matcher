<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsInDateFormat;
use PHPUnit\Framework\TestCase;

class IsInDateFormatTest extends TestCase
{
    /**
     * @dataProvider examplesDatesProvider
     */
    public function test_dates(string $date, string $format, bool $result) : void
    {
        $expander = new IsInDateFormat($format);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($result, $expander->match($date));
    }

    public static function examplesDatesProvider() : array
    {
        return [
            ['2010-01-01', 'Y-m-d', true],
            ['2010-01-01 00:00:01', 'Y-m-d H:i:s', true],
            ['2010-01-01 00:00:01', 'Y-m-d', false],
            ['20100101', 'Ymd', true],
            ['Y-m-d', 'Y-m-d', false],
        ];
    }
}
