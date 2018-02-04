<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsEmail;
use PHPUnit\Framework\TestCase;

class IsEmailTest extends TestCase
{
    /**
     * @dataProvider examplesEmailsProvider
     */
    public function test_emails($email, $expectedResult)
    {
        $expander = new IsEmail();
        $this->assertEquals($expectedResult, $expander->match($email));
    }

    public static function examplesEmailsProvider()
    {
        return [
            ['valid@email.com', true],
            ['valid+12345@email.com', true],
            ['...@domain.com', false],
            ['2222----###@domain.co', true]
        ];
    }
}
