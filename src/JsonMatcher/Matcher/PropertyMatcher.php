<?php

namespace JsonMatcher\Matcher;

interface PropertyMatcher
{
    public function match($matcher, $pattern);

    public function canMatch($pattern);

}