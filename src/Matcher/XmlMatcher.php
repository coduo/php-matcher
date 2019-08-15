<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Xml;
use LSS\XML2Array;

final class XmlMatcher extends Matcher
{
    private $arrayMatcher;

    public function __construct(ArrayMatcher $arrayMatcher)
    {
        $this->arrayMatcher = $arrayMatcher;
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

        $match = $this->arrayMatcher->match($arrayValue, $arrayPattern);
        if (!$match) {
            $this->error = $this->arrayMatcher->getError();
            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        return Xml::isValid($pattern);
    }
}
