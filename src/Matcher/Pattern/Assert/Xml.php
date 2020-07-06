<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Assert;

use SimpleXMLElement;
use function is_string;
use function simplexml_load_string;

final class Xml
{
    public static function isValid($value) : bool
    {
        if (!is_string($value)) {
            return false;
        }
        $xml = @simplexml_load_string($value);
        return $xml instanceof SimpleXMLElement;
    }
}
