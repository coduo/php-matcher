<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;
use Symfony\Component\Validator\Constraints\Ulid;

final class UlidMatcher extends Matcher
{
    /**
     * @var string
     */
    public const PATTERN = 'ulid';

    /**
     * ULID validation pattern highly inspired by the Symfony Uid Component.
     *
     * @see https://github.com/symfony/uid/blob/8311a3f6e14c21960e7955452fe52a462d58ad2b/Ulid.php#L41-L52
     *
     * @var string
     */
    public const ULID_PATTERN = '[01234567]{1}[0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz]{25}';

    private Backtrace $backtrace;

    private Parser $parser;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->parser = $parser;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!\is_string($value)) {
            $this->error = \sprintf(
                '%s "%s" is not a valid ULID: not a string.',
                \gettype($value),
                new StringConverter($value)
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (\strlen($value) !== \strspn($value, '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz')) {
            $this->error = \sprintf(
                '%s "%s" is not a valid ULID: invalid characters.',
                \gettype($value),
                new StringConverter($value)
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (26 < \strlen($value)) {
            $this->error = \sprintf(
                '%s "%s" is not a valid ULID: too long.',
                \gettype($value),
                new StringConverter($value)
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (26 > \strlen($value)) {
            $this->error = \sprintf(
                '%s "%s" is not a valid ULID: too short.',
                \gettype($value),
                new StringConverter($value)
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        // Largest valid ULID is '7ZZZZZZZZZZZZZZZZZZZZZZZZZ'
        // Cf https://github.com/ulid/spec#overflow-errors-when-parsing-base32-strings
        if ($value[0] > '7') {
            $this->error = \sprintf(
                '%s "%s" is not a valid ULID: overflow.',
                \gettype($value),
                new StringConverter($value)
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        if (!\is_string($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        $result = $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::PATTERN);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
