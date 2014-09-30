<?php

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\JsonMatcher;
use Coduo\PHPMatcher\Matcher\NullMatcher;
use Coduo\PHPMatcher\Matcher\ScalarMatcher;
use Coduo\PHPMatcher\Matcher\TypeMatcher;
use Coduo\PHPMatcher\Matcher\WildcardMatcher;
use Coduo\PHPMatcher\Matcher\XmlMatcher;

class XmlMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonMatcher
     */
    private $matcher;

    public function setUp()
    {
        $scalarMatchers = new ChainMatcher(array(
            new TypeMatcher(),
            new ScalarMatcher(),
            new NullMatcher(),
            new WildcardMatcher()
        ));
        $this->matcher = new XmlMatcher(new ChainMatcher(array(
            $scalarMatchers,
            new ArrayMatcher($scalarMatchers)
        )));
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
        $this->assertTrue($this->matcher->match($value, $pattern), $this->matcher->getError());
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern), $this->matcher->getError());

    }

    public static function positivePatterns()
    {
        return array(
            array('<xml></xml>'),
            array('<users><user>@string@</user></users>'),
        );
    }

    public static function negativePatterns()
    {
        return array(
            array('<xml '),
            array('asdkasdasdqwrq'),
        );
    }

    public static function positiveMatches()
    {
        return array(
            array(
                '<users><user>Norbert</user><user>Michał</user></users>',
                '<users><user>@string@</user><user>@string@</user></users>'
            ),
            array(
                '<users><user id="1">Norbert</user></users>',
                '<users><user id="@string@">@string@</user></users>'
            ),
            array(
                '<users><user><name>Norbert</name><age>25</age></user></users>',
                '<users><user><name>Norbert</name><age>@string@</age></user></users>'
            ),
            array(
                '<string><![CDATA[Any kid of text here]]></string>',
                '<string><![CDATA[@string@]]></string>'
            ),
            array(
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
            )
        );
    }

    public static function negativeMatches()
    {
        return array(
            array(
                '<users><user>Norbert</user><user>Michał</user></users>',
                '{"users":["Michał","@string@"]}'
            ),
            array(
                '<users><user>Norbert</user><user>Michał</user></users>',
                '<users><user>@integer@</user><user>@integer@</user></users>'
            ),
        );
    }
}
