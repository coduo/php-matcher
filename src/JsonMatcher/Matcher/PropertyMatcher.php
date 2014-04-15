<?php

namespace JsonMatcher\Matcher;

interface PropertyMatcher
{
    public function match($value, $pattern);

    public function canMatch($pattern);

}
