<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsTzOffset;
use PHPUnit\Framework\TestCase;

class IsTzOffsetTest extends TestCase
{
    public static function examplesDatesProvider()
    {
        return [
            ['201-20-44', false, 'Timezone expander require valid timezone, got "201-20-44".'],
            ['GMT', false, 'Timezone "GMT" is not an offset type.'],
            ['Europe/Warsaw', false, 'Timezone "Europe/Warsaw" is not an offset type.'],
            ['00:00', true],
            ['invalid', false, 'Timezone expander require valid timezone, got "invalid".'],
            ['-01:00', true],
        ];
    }

    /**
     * @dataProvider examplesDatesProvider
     */
    public function test_dates(string $date, bool $expectedResult, ?string $errorMessage = null) : void
    {
        $expander = new IsTzOffset();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertSame($expectedResult, $expander->match($date));
        if ($expectedResult === false) {
            $this->assertSame($expander->getError(), $errorMessage);
        }
    }
}
