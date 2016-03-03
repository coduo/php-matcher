<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Xml;
use LSS\XML2Array;

final class XmlMatcher extends Matcher
{
    /**
     * @var
     */
    private $matcher;

    /**
     * @param ValueMatcher $matcher
     */
    public function __construct(ValueMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (parent::match($value, $pattern)) {
            return true;
        }

        if (!Xml::isValid($value) || !Xml::isValid($pattern)) {
            return false;
        }

        $arrayValue = XML2Array::createArray($value);
        $arrayPattern = XML2Array::createArray($pattern);

        $match = $this->matcher->match($arrayValue, $arrayPattern);
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
        return Xml::isValid($pattern);
    }
}
