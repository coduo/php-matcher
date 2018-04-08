<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class XmlMatcherTest extends TestCase
{
    /**
     * @var Matcher\XmlMatcher
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

        $this->matcher = new Matcher\XmlMatcher(
            new Matcher\ChainMatcher(
                [
                $scalarMatchers,
                new Matcher\ArrayMatcher($scalarMatchers, $parser)
                ]
            )
        );
    }

    /**
     * @dataProvider positivePatterns
     */
    public function test_positive_can_match($pattern)
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativePatterns
     */
    public function test_negative_can_match($pattern)
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_positive_matches($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern), (string) $this->matcher->getError());
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern), (string) $this->matcher->getError());
    }

    public static function positivePatterns()
    {
        return [
            ['<xml></xml>'],
            ['<users><user>@string@</user></users>'],
        ];
    }

    public static function negativePatterns()
    {
        return [
            ['<xml '],
            ['asdkasdasdqwrq'],
        ];
    }

    public static function positiveMatches()
    {
        return [
            [
                '<users><user>Norbert</user><user>Michał</user></users>',
                '<users><user>@string@</user><user>@string@</user></users>'
            ],
            [
                '<users><user id="1">Norbert</user></users>',
                '<users><user id="@string@">@string@</user></users>'
            ],
            [
                '<users><user><name>Norbert</name><age>25</age></user></users>',
                '<users><user><name>Norbert</name><age>@string@</age></user></users>'
            ],
            [
                '<string><![CDATA[Any kind of text here]]></string>',
                '<string><![CDATA[@string@]]></string>'
            ],
            [
                <<<XML
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>IBM</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML
            ,
                <<<XML
<?xml version="1.0"?>
<soap:Envelope
    xmlns:soap="@string@"
            soap:encodingStyle="@string@">

<soap:Body xmlns:m="@string@">
  <m:GetStockPrice>
    <m:StockName>@string@</m:StockName>
    <m:StockValue>@string@</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML
            ]
        ];
    }

    public static function negativeMatches()
    {
        return [
            [
                '<users><user>Norbert</user><user>Michał</user></users>',
                '{"users":["Michał","@string@"]}'
            ],
            [
                '<users><user>Norbert</user><user>Michał</user></users>',
                '<users><user>@integer@</user><user>@integer@</user></users>'
            ],
        ];
    }
}
