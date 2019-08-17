<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;

trait BacktraceBehavior
{
    /**
     * @var Backtrace
     */
    protected $backtrace;

    public function setBacktrace(Backtrace $backtrace) : void
    {
        $this->backtrace = $backtrace;
    }
}
