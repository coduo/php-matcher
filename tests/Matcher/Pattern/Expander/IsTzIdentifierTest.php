<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsTzIdentifier;
use PHPUnit\Framework\TestCase;

class IsTzIdentifierTest extends TestCase
{
    public static function examplesDatesProvider()
    {
        return [
            ['201-20-44', false, 'Timezone expander require valid timezone, got "201-20-44".'],
            ['GMT', false, 'Timezone "GMT" is not an identifier type.'],
            ['00:00', false, 'Timezone "00:00" is not an identifier type.'],
            ['Europe/Warsaw', true],
            ['invalid', false, 'Timezone expander require valid timezone, got "invalid".'],
        ];
    }

    /**
     * @dataProvider examplesDatesProvider
     */
    public function test_dates(string $date, bool $expectedResult, ?string $errorMessage = null) : void
    {
        $expander = new IsTzIdentifier();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertSame($expectedResult, $expander->match($date));

        if ($expectedResult === false) {
            $this->assertSame($expander->getError(), $errorMessage);
        }
    }
}
