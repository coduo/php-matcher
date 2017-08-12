<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Xml;
use LSS\XML2Array;

final class XmlMatcher extends Matcher
{
    private $matcher;

    public function __construct(ValueMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function match($value, $pattern) : bool
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

    public function canMatch($pattern) : bool
    {
        return Xml::isValid($pattern);
    }
}
