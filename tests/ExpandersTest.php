<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

final class ExpandersTest extends TestCase
{
    protected ?PHPMatcher $matcher = null;

    public static function expanderExamples()
    {
        return [
            ['lorem ipsum', '@string@.startsWith("lorem")', true],
            ['lorem ipsum', '@string@.startsWith("LOREM", true)', true],
            ['lorem ipsum', '@string@.endsWith("ipsum")', true],
            ['lorem ipsum', '@string@.endsWith("IPSUM", true)', true],
            ['lorem ipsum', '@string@.contains("lorem")', true],
            ['norbert@coduo.pl', '@string@.isEmail()', true],
            ['lorem ipsum', '@string@.isEmail()', false],
            ['http://coduo.pl/', '@string@.isUrl()', true],
            ['lorem ipsum', '@string@.isUrl()', false],
            ['2014-08-19', '@string@.isDateTime()', true],
            ['3014-08-19', '@string@.before("today")', false],
            ['1014-08-19', '@string@.before("+ 1day")', true],
            ['3014-08-19', '@string@.after("today")', true],
            ['1014-08-19', '@string@.after("+ 1day")', false],
            [100, '@integer@.lowerThan(101).greaterThan(10)', true],
            ['', '@string@.isNotEmpty()', false],
            ['lorem ipsum', '@string@.isNotEmpty()', true],
            ['', '@string@.isEmpty()', true],
            [['foo', 'bar'], '@array@.inArray("bar")', true],
            [[], '@array@.isEmpty()', true],
            [[], ['@string@'], false],
            [[], ['@string@.optional()'], true],
            [['foo'], '@array@.isEmpty()', false],
            [[1, 2, 3], '@array@.count(3)', true],
            [[1, 2, 3], '@array@.count(4)', false],
            ['lorem ipsum', '@string@.oneOf(contains("lorem"), contains("test"))', true],
            ['lorem ipsum', '@string@.oneOf(contains("lorem"), contains("test")).endsWith("ipsum")', true],
            ['lorem ipsum', '@string@.matchRegex("/^lorem \\w+$/")', true],
            ['lorem ipsum', '@string@.matchRegex("/^foo/")', false],
            [[], ['unexistent_key' => '@array@.optional()'], true],
            [[], ['unexistent_key' => '@boolean@.optional()'], true],
            [[], ['unexistent_key' => '@double@.optional()'], true],
            [[], ['unexistent_key' => '@integer@.optional()'], true],
            [[], ['unexistent_key' => '@json@.optional()'], true],
            [[], ['unexistent_key' => '@number@.optional()'], true],
            [[], ['unexistent_key' => '@scalar@.optional()'], true],
            [[], ['unexistent_key' => '@string@.optional()'], true],
            [[], ['unexistent_key' => '@text@.optional()'], true],
            [[], ['unexistent_key' => '@uuid@.optional()'], true],
            [[], ['unexistent_key' => '@ulid@.optional()'], true],
            [[], ['unexistent_key' => '@xml@.optional()'], true],
            [[], ['unexistent_key' => '@array@.optional()', 'unexistent_second_key' => '@string@.optional()'], true],
            [[], ['unexistent_key' => '@array@.optional()', 'unexistent_second_key' => '@string@'], false],
            [['Norbert', 'Michał'], '@array@.repeat("@string@")', true],
            ['127.0.0.1', '@string@.isIp()', true],
            ['2001:0db8:0000:42a1:0000:0000:ab1c:0001', '@string@.isIp()', true],
            ['127.255.999.999', '@string@.isIp()', false],
            ['foo:bar:42:42', '@string@.isIp()', false],
            ['{"image":{"url":"http://image.com"}}', '{"image":"@json@.match({\"url\":\"@string@.isUrl()\"})"}', true],
            ['{"image":null}', '{"image":"@null@||@json@.match({\"url\":\"@string@.isUrl()\"})"}', true],
            ['{"image":null}', '{"image":"@json@.oneOf(optional(), match({\"url\":\"@string@.isUrl()\"}) )"}', true],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new PHPMatcher();
    }

    /**
     * @dataProvider expanderExamples()
     */
    public function test_expanders($value, $pattern, $expectedResult) : void
    {
        $this->assertSame($expectedResult, $this->matcher->match($value, $pattern), (string) $this->matcher->error());
    }
}
