<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use PHPUnit\Framework\TestCase;

class SimpleFactoryTest extends TestCase
{
    public function test_creating_matcher()
    {
        $factory = new SimpleFactory();
        $this->assertInstanceOf('Coduo\PHPMatcher\Matcher', $factory->createMatcher());
    }
}
