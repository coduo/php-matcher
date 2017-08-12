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
        return array(
            array("valid@email.com", true),
            array("valid+12345@email.com", true),
            array("...@domain.com", false),
            array("2222----###@domain.co", true)
        );
    }
}
