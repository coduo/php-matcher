<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class TextMatcherTest extends TestCase
{
    /**
     * @var Matcher\TextMatcher
     */
    private $matcher;

    public function setUp()
    {
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
        $scalarMatchers = new Matcher\ChainMatcher([
            new Matcher\CallbackMatcher(),
            new Matcher\ExpressionMatcher(),
            new Matcher\NullMatcher(),
            new Matcher\StringMatcher($parser),
            new Matcher\IntegerMatcher($parser),
            new Matcher\BooleanMatcher($parser),
            new Matcher\DoubleMatcher($parser),
            new Matcher\NumberMatcher($parser),
            new Matcher\ScalarMatcher(),
            new Matcher\WildcardMatcher(),
        ]);
        $this->matcher = new Matcher\TextMatcher(
            new Matcher\ChainMatcher([
                $scalarMatchers,
                new Matcher\ArrayMatcher($scalarMatchers, $parser)
            ]),
            $parser
        );
    }

    /**
     * @dataProvider matchingData
     */
    public function test_positive_matches($value, $pattern, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->matcher->match($value, $pattern));
    }

    public function test_ignore_valid_json_patterns()
    {
        $jsonPattern = \json_encode([
            'users' => [
                ['id' => '@number@', 'name' => 'Norbert'],
                ['id' => '@number@', 'name' => 'Michal']
            ]
        ]);

        $this->assertFalse($this->matcher->canMatch($jsonPattern));
    }

    public function test_ignore_valid_xml_patterns()
    {
        $xmlPattern = <<<XML
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

    public function test_error_when_unsupported_type_pattern_used()
    {
        $pattern = 'lorem ipsum @null@ bla bla';
        $value = 'lorem ipsum bla bla';

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertSame('Type pattern "@null@" is not supported by TextMatcher.', $this->matcher->getError());
    }

    public function matchingData()
    {
        return [
            [
                'lorem ipsum lol lorem 24 dolorem',
                'lorem ipsum @string@.startsWith("lo") lorem @number@ dolorem',
                true
            ],
            [
                'lorem ipsum 24 dolorem',
                'lorem ipsum @integer@',
                false
            ],
            [
                '/users/12345/active',
                '/users/@integer@.greaterThan(0)/active',
                true
            ],
            [
                '/user/ebd1fb0e-45ae-11e8-842f-0ed5f89f718b/profile',
                '/user/@uuid@/@string@',
                true
            ],
            [
                '/user/12345/profile',
                '/user/@uuid@/@string@',
                false
            ]
        ];
    }
}
