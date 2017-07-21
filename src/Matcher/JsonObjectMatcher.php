<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class JsonObjectMatcher extends Matcher
{
    const JSON_PATTERN = '@json@';

    /**
     * @var ValueMatcher
     */
    private $propertyMatcher;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param ValueMatcher $propertyMatcher
     */
    public function __construct(ValueMatcher $propertyMatcher, Parser $parser)
    {
        $this->propertyMatcher = $propertyMatcher;
        $this->parser = $parser;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!$this->isJsonPattern($pattern)) {
            return false;
        }

        if (!is_array($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid array.", gettype($value), new StringConverter($value));
            return false;
        }

        if ($this->isJsonPattern($pattern)) {
            return $this->allExpandersMatch($value, $pattern);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && $this->isJsonPattern($pattern);
    }

    private function isJsonPattern($pattern)
    {
        if (!is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is('json');
    }

    /**
     * @param $value
     * @param $pattern
     * @return bool
     * @throws \Coduo\PHPMatcher\Exception\UnknownExpanderException
     */
    private function allExpandersMatch($value, $pattern)
    {
        $typePattern = $this->parser->parse($pattern);
        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            return false;
        }

        return true;
    }
}
