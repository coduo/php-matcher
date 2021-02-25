<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\ArrayMatcher;

final class Diff
{
    /**
     * @var Difference[]
     */
    private array $differences;

    public function __construct(Difference ...$difference)
    {
        $this->differences = $difference;
    }

    public function add(Difference $difference) : self
    {
        return new self(...\array_merge($this->differences, [$difference]));
    }

    /**
     *  @return Difference[]
     */
    public function all() : array
    {
        return $this->differences;
    }

    public function count() : int
    {
        return \count($this->differences);
    }
}
