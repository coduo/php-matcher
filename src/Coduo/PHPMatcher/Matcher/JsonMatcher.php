<?php

namespace Coduo\PHPMatcher\Matcher;

class JsonMatcher extends Matcher
{
    const TRANSFORM_QUOTATION_PATTERN = '/([^"])@(integer|string|array|double|wildcard|boolean)@([^"])/';
    const TRANSFORM_QUOTATION_REPLACEMENT = '$1"@$2@"$3';

    /**
     * @var
     */
    private $matcher;

    /**
     * @param PropertyMatcher $matcher
     */
    public function __construct(PropertyMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_string($value) || !$this->isValidJson($value)) {
            return false;
        }

        $pattern = $this->transformPattern($pattern);
        $match = $this->matcher->match(json_decode($value, true), json_decode($pattern, true));
        if (!$match) {
            $this->error = $this->matcher->getError();
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        if (!is_string($pattern)) {
            return false;
        }

        return $this->isValidJson($this->transformPattern($pattern));
    }

    private function isValidJson($string)
    {
        @json_decode($string, true);

        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Wraps placeholders which arent wrapped with quotes yet
     *
     * @param $pattern
     * @return mixed
     */
    private function transformPattern($pattern)
    {
        return preg_replace(self::TRANSFORM_QUOTATION_PATTERN, self::TRANSFORM_QUOTATION_REPLACEMENT, $pattern);
    }

}
