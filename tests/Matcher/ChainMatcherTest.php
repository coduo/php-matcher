<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;

class ChainMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayMatcher
     */
    private $matcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $firstMatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $secondMatcher;

    public function setUp()
    {
        $this->firstMatcher = $this->getMock('Coduo\PHPMatcher\Matcher\ValueMatcher');
        $this->secondMatcher = $this->getMock('Coduo\PHPMatcher\Matcher\ValueMatcher');

        $this->matcher = new ChainMatcher(array(
            $this->firstMatcher,
            $this->secondMatcher
        ));
    }

    public function test_only_one_matcher_can_match_but_none_matchers_match()
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->firstMatcher->expects($this->never())->method('match');
        $this->secondMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(true));
        $this->secondMatcher->expects($this->once())->method('match')->will($this->returnValue(false));

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
    }

    public function test_none_matchers_can_match()
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->firstMatcher->expects($this->never())->method('match');
        $this->secondMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->secondMatcher->expects($this->never())->method('match');

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
    }

    public function test_first_matcher_match()
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(true));
        $this->firstMatcher->expects($this->once())->method('match')->will($this->returnValue(true));
        $this->secondMatcher->expects($this->never())->method('canMatch');
        $this->secondMatcher->expects($this->never())->method('match');

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), true);
    }

    public function test_if_there_is_error_description_only_from_last_matcher_that_fails()
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

    public function test_error_description_when_any_matcher_can_match()
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));
        $this->secondMatcher->expects($this->once())->method('canMatch')->will($this->returnValue(false));

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
        $this->assertEquals($this->matcher->getError(), 'Any matcher from chain can\'t match value "foo" to pattern "foo_pattern"');
    }
}
