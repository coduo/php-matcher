<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Value;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;

final class SingleLineString
{
    private string $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    public function __toString() : string
    {
        $normalized = $this->raw;

        if (Json::isValid($this->raw)) {
            $normalized = Json::reformat($this->raw);
        } elseif (Json::isValid(Json::transformPattern($this->raw))) {
            $normalized = Json::reformat(Json::transformPattern($this->raw));
        }

        return \str_replace(["\r\n", "\r", "\n"], ' ', $normalized);
    }
}
