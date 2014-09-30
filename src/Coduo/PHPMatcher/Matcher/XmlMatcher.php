<?php

namespace Coduo\PHPMatcher\Matcher;

use LSS\XML2Array;

class XmlMatcher extends Matcher
{
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
        if (!is_string($value) || !$this->isValidXml($value) || !$this->isValidXml($pattern)) {
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
        if (!is_string($pattern)) {
            return false;
        }

        return $this->isValidXml($pattern);
    }

    private function isValidXml($string)
    {
        $xml = @simplexml_load_string($string);

        if (!$xml instanceof \SimpleXMLElement) {

            return false;
        }

        return true;
    }
}
