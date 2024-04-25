<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class TextMatcherTest extends TestCase
{
    private ?Matcher\TextMatcher $matcher = null;

    public static function matchingData()
    {
        return [
            [
                'lorem ipsum lol lorem 24 dolorem',
                'lorem ipsum @string@.startsWith("lo") lorem @number@ dolorem',
                true,
            ],
            [
                'lorem ipsum 24 dolorem',
                'lorem ipsum @integer@',
                false,
            ],
            [
                '/users/12345/active',
                '/users/@integer@.greaterThan(0)/active',
                true,
            ],
            [
                '/user/ebd1fb0e-45ae-11e8-842f-0ed5f89f718b/profile',
                '/user/@uuid@/@string@',
                true,
            ],
            [
                '/user/12345/profile',
                '/user/@uuid@/@string@',
                false,
            ],
            [
                '/user/01BX5ZZKBKACTAV9WEVGEMMVS0/profile',
                '/user/@ulid@/@string@',
                true,
            ],
            [
                '/user/12345/profile',
                '/user/@ulid@/@string@',
                false,
            ],
            [
                '/user/8ZZZZZZZZZZZZZZZZZZZZZZZZZ/profile',
                '/user/@ulid@/@string@',
                false,
            ],
        ];
    }

    public function setUp() : void
    {
        $backtrace = new Backtrace\InMemoryBacktrace();

        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace));
        $this->matcher = new Matcher\TextMatcher(
            $backtrace,
            $parser
        );
    }

    /**
     * @dataProvider matchingData
     */
    public function test_positive_matches($value, $pattern, $expectedResult) : void
    {
        $this->assertEquals($expectedResult, $this->matcher->match($value, $pattern));
    }

    public function test_ignore_valid_json_patterns() : void
    {
        $jsonPattern = \json_encode([
            'users' => [
                ['id' => '@number@', 'name' => 'Norbert'],
                ['id' => '@number@', 'name' => 'Michal'],
            ],
        ]);

        $this->assertFalse($this->matcher->canMatch($jsonPattern));
    }

    public function test_ignore_valid_xml_patterns() : void
    {
        $xmlPattern = <<<'XML'
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>@string@</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;

        $this->assertFalse($this->matcher->canMatch($xmlPattern));
    }

    public function test_error_when_unsupported_type_pattern_used() : void
    {
        $pattern = 'lorem ipsum @null@ bla bla';
        $value = 'lorem ipsum bla bla';

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertSame('Type pattern "@null@" is not supported by TextMatcher.', $this->matcher->getError());
    }
}
