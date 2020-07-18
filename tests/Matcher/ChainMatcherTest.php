<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\ValueMatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ChainMatcherTest extends TestCase
{
    /**
     * @var ArrayMatcher
     */
    private $matcher;

    /**
     * @var MockObject
     */
    private $firstMatcher;

    /**
     * @var MockObject
     */
    private $secondMatcher;

    public function setUp() : void
    {
        $this->firstMatcher = $this->createMock(ValueMatcher::class);
        $this->secondMatcher = $this->createMock(ValueMatcher::class);

        $this->matcher = new ChainMatcher(
            self::class,
            new Backtrace\InMemoryBacktrace(),
            [
                $this->firstMatcher,
                $this->secondMatcher,
            ]
        );
    }

    public function test_only_one_matcher_can_match_but_none_matchers_match() : void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->firstMatcher->expects($this->never())->method('match');
        $this->secondMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(true));
        $this->secondMatcher->expects($this->once())->method('match')->will($this->returnValue(false));

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
    }

    public function test_none_matchers_can_match() : void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->firstMatcher->expects($this->never())->method('match');
        $this->secondMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->secondMatcher->expects($this->never())->method('match');

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
    }

    public function test_first_matcher_match() : void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(true));
        $this->firstMatcher->expects($this->once())->method('match')->will($this->returnValue(true));
        $this->secondMatcher->expects($this->never())->method('canMatch');
        $this->secondMatcher->expects($this->never())->method('match');

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), true);
    }

    public function test_if_there_is_error_description_only_from_last_matcher_that_fails() : void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(true));
        $this->firstMatcher->expects($this->once())->method('match')->will($this->returnValue(false));
        $this->firstMatcher->expects($this->once())->method('getError')
            ->will($this->returnValue('First matcher error'));

        $this->secondMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(true));
        $this->secondMatcher->expects($this->once())->method('match')->will($this->returnValue(false));
        $this->secondMatcher->expects($this->once())->method('getError')
            ->will($this->returnValue('Second matcher error'));

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
        $this->assertEquals($this->matcher->getError(), 'Second matcher error');
    }

    public function test_error_description_when_any_matcher_can_match() : void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->secondMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
        $this->assertEquals($this->matcher->getError(), 'Any matcher from chain can\'t match value "foo" to pattern "foo_pattern"');
    }
}
