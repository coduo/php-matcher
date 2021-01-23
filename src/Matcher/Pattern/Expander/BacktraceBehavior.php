<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;

trait BacktraceBehavior
{
    protected \Coduo\PHPMatcher\Backtrace $backtrace;

    public function setBacktrace(Backtrace $backtrace) : void
    {
        $this->backtrace = $backtrace;
    }
}
