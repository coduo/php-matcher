<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use PHPUnit\Framework\TestCase;

class SimpleFactoryTest extends TestCase
{
    public function test_creating_matcher()
    {
        $factory = new SimpleFactory();
        $this->assertInstanceOf(Matcher::class, $factory->createMatcher());
    }
}
