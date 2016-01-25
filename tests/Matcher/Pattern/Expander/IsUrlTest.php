<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsUrl;

class IsUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesUrlsProvider
     */
    public function test_urls($url, $expectedResult)
    {
        $expander = new IsUrl();
        $this->assertEquals($expectedResult, $expander->match($url));
    }

    public static function examplesUrlsProvider()
    {
        return array(
            array("http://example.com/test.html", true),
            array("https://example.com/test.html", true),
            array("https://example.com/user/{id}/", true),
            array("mailto:email@example.com", true),
            array("//example.com/test/", false),
            array("example", false),
            array("", false)
        );
    }
}
