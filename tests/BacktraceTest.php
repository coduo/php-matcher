<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Factory\MatcherFactory;
use Coduo\PHPMatcher\Matcher;
use PHPUnit\Framework\TestCase;

final class BacktraceTest extends TestCase
{
    /**
     * @var Matcher
     */
    protected $matcher;

    public function setUp() : void
    {
        $factory = new MatcherFactory();
        $this->matcher = $factory->createMatcher();
    }

    public function test_backtrace_in_failed_simple_matching()
    {
        $this->matcher->match(100, '@string@');

        $this->assertSame(
            <<<FAILED_BACKTRACE
#1 Matcher Coduo\PHPMatcher\Matcher matching value "100" with "@string@" pattern
#2 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) matching value "100" with "@string@" pattern
#3 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@string@"
#4 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "100" with "@string@" pattern
#5 Matcher Coduo\PHPMatcher\Matcher\CallbackMatcher can't match pattern "@string@"
#6 Matcher Coduo\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "@string@"
#7 Matcher Coduo\PHPMatcher\Matcher\NullMatcher can't match pattern "@string@"
#8 Matcher Coduo\PHPMatcher\Matcher\StringMatcher can match pattern "@string@"
#9 Matcher Coduo\PHPMatcher\Matcher\StringMatcher matching value "100" with "@string@" pattern
#10 Matcher Coduo\PHPMatcher\Matcher\StringMatcher failed to match value "100" with "@string@" pattern
#11 Matcher Coduo\PHPMatcher\Matcher\StringMatcher error: integer "100" is not a valid string.
#12 Matcher Coduo\PHPMatcher\Matcher\IntegerMatcher can't match pattern "@string@"
#13 Matcher Coduo\PHPMatcher\Matcher\BooleanMatcher can't match pattern "@string@"
#14 Matcher Coduo\PHPMatcher\Matcher\DoubleMatcher can't match pattern "@string@"
#15 Matcher Coduo\PHPMatcher\Matcher\NumberMatcher can't match pattern "@string@"
#16 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher can match pattern "@string@"
#17 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher matching value "100" with "@string@" pattern
#18 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher failed to match value "100" with "@string@" pattern
#19 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher error: "100" does not match "@string@".
#20 Matcher Coduo\PHPMatcher\Matcher\WildcardMatcher can't match pattern "@string@"
#21 Matcher Coduo\PHPMatcher\Matcher\UuidMatcher can't match pattern "@string@"
#22 Matcher Coduo\PHPMatcher\Matcher\JsonObjectMatcher can't match pattern "@string@"
#23 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) failed to match value "100" with "@string@" pattern
#24 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) error: "100" does not match "@string@".
#25 Matcher Coduo\PHPMatcher\Matcher\JsonMatcher can't match pattern "@string@"
#26 Matcher Coduo\PHPMatcher\Matcher\XmlMatcher can't match pattern "@string@"
#27 Matcher Coduo\PHPMatcher\Matcher\OrMatcher can't match pattern "@string@"
#28 Matcher Coduo\PHPMatcher\Matcher\TextMatcher can match pattern "@string@"
#29 Matcher Coduo\PHPMatcher\Matcher\TextMatcher matching value "100" with "@string@" pattern
#30 Matcher Coduo\PHPMatcher\Matcher\TextMatcher failed to match value "100" with "@string@" pattern
#31 Matcher Coduo\PHPMatcher\Matcher\TextMatcher error: integer "100" is not a valid string.
#32 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) failed to match value "100" with "@string@" pattern
#33 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) error: integer "100" is not a valid string.
#34 Matcher Coduo\PHPMatcher\Matcher failed to match value "100" with "@string@" pattern
#35 Matcher Coduo\PHPMatcher\Matcher error: integer "100" is not a valid string.
FAILED_BACKTRACE
            ,
            (string) $this->matcher->backtrace()
        );
    }

    public function test_backtrace_in_succeed_simple_matching()
    {
        $this->matcher->match('100', '@string@');

        $this->assertSame(
            <<<SUCCEED_BACKTRACE
#1 Matcher Coduo\PHPMatcher\Matcher matching value "100" with "@string@" pattern
#2 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) matching value "100" with "@string@" pattern
#3 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@string@"
#4 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "100" with "@string@" pattern
#5 Matcher Coduo\PHPMatcher\Matcher\CallbackMatcher can't match pattern "@string@"
#6 Matcher Coduo\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "@string@"
#7 Matcher Coduo\PHPMatcher\Matcher\NullMatcher can't match pattern "@string@"
#8 Matcher Coduo\PHPMatcher\Matcher\StringMatcher can match pattern "@string@"
#9 Matcher Coduo\PHPMatcher\Matcher\StringMatcher matching value "100" with "@string@" pattern
#10 Matcher Coduo\PHPMatcher\Matcher\StringMatcher successfully matched value "100" with "@string@" pattern
#11 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) successfully matched value "100" with "@string@" pattern
#12 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) successfully matched value "100" with "@string@" pattern
#13 Matcher Coduo\PHPMatcher\Matcher successfully matched value "100" with "@string@" pattern
SUCCEED_BACKTRACE
            ,
            (string) $this->matcher->backtrace()
        );
    }

    public function test_backtrace_in_failed_complex_matching()
    {
        $this->matcher->match(
            /** @lang JSON */
            '{
                "users":[
                    {
                        "id": 131,
                        "firstName": "Norbert",
                        "lastName": "Orzechowicz",
                        "enabled": true,
                        "roles": ["ROLE_DEVELOPER"]
                    },
                    {
                        "id": 132,
                        "firstName": "Michał",
                        "lastName": "Dąbrowski",
                        "enabled": false,
                        "roles": ["ROLE_DEVELOPER"]
                    }
                ],
                "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
            }',
            /** @lang JSON */
            '{
                "users":[
                    {
                        "id": "@integer@",
                        "firstName":"Norbert",
                        "lastName":"Orzechowicz",
                        "enabled": "@boolean@",
                        "roles": "@array@"
                    },
                    {
                        "id": "@integer@",
                        "firstName": "Michał",
                        "lastName": "Dąbrowski",
                        "enabled": "expr(value == true)",
                        "roles": "@array@"
                    }
                ],
                "prevPage": "@string@",
                "nextPage": "@string@"
            }'
        );

        // Uncomment when backtrace logic changes, run tests and then commit again.
        //\file_put_contents(__DIR__ . '/BacktraceTest/failed_complex_matching_expected_trace.txt', (string) $this->matcher->backtrace());

        $this->assertSame(
            \file_get_contents(__DIR__ . '/BacktraceTest/failed_complex_matching_expected_trace.txt'),
            (string) $this->matcher->backtrace()
        );
    }

    public function test_backtrace_in_succeed_complex_matching()
    {
        $this->matcher->match(
        /** @lang JSON */
            '{
                "users":[
                    {
                        "id": 131,
                        "firstName": "Norbert",
                        "lastName": "Orzechowicz",
                        "enabled": true,
                        "roles": ["ROLE_DEVELOPER"]
                    },
                    {
                        "id": 132,
                        "firstName": "Michał",
                        "lastName": "Dąbrowski",
                        "enabled": false,
                        "roles": ["ROLE_DEVELOPER"]
                    }
                ],
                "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
            }',
            /** @lang JSON */
            '{
                "users":[
                    {
                        "id": "@integer@",
                        "firstName":"Norbert",
                        "lastName":"Orzechowicz",
                        "enabled": "@boolean@",
                        "roles": "@array@"
                    },
                    {
                        "id": "@integer@",
                        "firstName": "Michał",
                        "lastName": "Dąbrowski",
                        "enabled": "expr(value == false)",
                        "roles": "@array@"
                    }
                ],
                "prevPage": "@string@",
                "nextPage": "@string@"
            }'
        );

        // Uncomment when backtrace logic changes, run tests and then commit again.
        //\file_put_contents(__DIR__ . '/BacktraceTest/succeed_complex_matching_expected_trace.txt', (string) $this->matcher->backtrace());

        $this->assertSame(
            \file_get_contents(__DIR__ . '/BacktraceTest/succeed_complex_matching_expected_trace.txt'),
            (string) $this->matcher->backtrace()
        );
    }
}
