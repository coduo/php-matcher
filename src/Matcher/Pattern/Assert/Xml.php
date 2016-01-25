<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Assert;

final class Xml
{
    /**
     * @param $value
     * @return bool
     */
    public static function isValid($value)
    {
        if (!is_string($value)) {
            return false;

        }
        $xml = @simplexml_load_string($value);
        if (!$xml instanceof \SimpleXMLElement) {
            return false;
        }

        return true;
    }
}
