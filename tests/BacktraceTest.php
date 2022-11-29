<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Backtrace\InMemoryBacktrace;
use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

final class BacktraceTest extends TestCase
{
    protected ?PHPMatcher $matcher = null;

    public function setUp() : void
    {
        $this->matcher = new PHPMatcher(new InMemoryBacktrace());
    }

    public function test_backtrace_in_failed_simple_matching() : void
    {
        $this->matcher->match(100, '@string@');

        $this->assertStringContainsString(
            "Matcher Coduo\PHPMatcher\Matcher error: integer \"100\" is not a valid string.",
            $this->matcher->backtrace()->last()
        );
    }

    public function test_backtrace_in_succeed_simple_matching() : void
    {
        $this->matcher->match('100', '@string@');

        $this->assertStringContainsString(
            "Matcher Coduo\PHPMatcher\Matcher successfully matched value \"100\" with \"@string@\" pattern",
            $this->matcher->backtrace()->last()
        );
    }

    public function test_backtrace_in_failed_complex_matching() : void
    {
        $this->matcher->match(
            /* @lang JSON */
            '{
                "users":[
                    {
                        "id": 131,
                        "firstName": "Norbert",
                        "lastName": "Orzechowicz",
                        "enabled": true,
                        "roles": []
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
            /* @lang JSON */
            '{
                "users":[
                    {
                        "id": "@integer@",
                        "firstName":"Norbert",
                        "lastName":"Orzechowicz",
                        "enabled": "@boolean@",
                        "roles": "@array@.isEmpty()"
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
        \file_put_contents(__DIR__ . '/BacktraceTest/failed_complex_matching_expected_trace.txt', (string) $this->matcher->backtrace());

        $this->assertSame(
            \file_get_contents(__DIR__ . '/BacktraceTest/failed_complex_matching_expected_trace.txt'),
            (string) $this->matcher->backtrace()
        );
    }

    public function test_backtrace_in_succeed_complex_matching() : void
    {
        $this->matcher->match(
            /* @lang JSON */
            '{
                "users":[
                    {
                        "id": 131,
                        "firstName": "Norbert",
                        "lastName": "Orzechowicz",
                        "enabled": true,
                        "roles": []
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
            /* @lang JSON */
            '{
                "users":[
                    {
                        "id": "@integer@",
                        "firstName":"Norbert",
                        "lastName":"Orzechowicz",
                        "enabled": "@boolean@",
                        "roles": "@array@.isEmpty()"
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
        \file_put_contents(__DIR__ . '/BacktraceTest/succeed_complex_matching_expected_trace.txt', (string) $this->matcher->backtrace());

        $this->assertSame(
            \file_get_contents(__DIR__ . '/BacktraceTest/succeed_complex_matching_expected_trace.txt'),
            (string) $this->matcher->backtrace()
        );
    }
}
