<?php
namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Factory\SimpleFactory;

class SimpleFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function test_creating_matcher()
    {
        $factory = new SimpleFactory();
        $this->assertInstanceOf('Coduo\PHPMatcher\Matcher', $factory->createMatcher());
    }
}
