<?php
namespace JsonMatcher;

class Matcher
{
    public function match($matcher, $matchTo)
    {

        if (is_array($matcher)) {
            if (-1 === $this->matchArray($matcher, $matchTo)) {
               return false;
            }
        } elseif(is_scalar($matcher) && strpos($matchTo, '@') > -1) {
            if (-1 === $this->matchType($matcher, $matchTo)) {
                return false;
            }
        } elseif (is_scalar($matcher)) {
            if (-1 === $this->matchScalar($matcher, $matchTo)) {
                return false;
            }
        }
        return true;
    }


    private function matchScalar($json, $expectedJson)
    {
        if ($json !== $expectedJson) {
            return -1;
        }
    }

    private function matchType($json, $type)
    {
        $type = str_replace("@", "", $type);

        if (gettype($json) !== $type) {
            return -1;
        }
    }


    private function matchArray(array $array, array $arrayToMatch)
    {

        if (count($array) !== count($arrayToMatch) ) {
            return -1;
        }

        $match = function($value, $array, $arrayToMatch) {

            $key = array_search($value, $arrayToMatch);
            if ($key !== false) {
                $this->match($array[$key], $arrayToMatch[$key]);

                return true;
            }

            $key = array_search($value, $array);
            if (is_scalar($arrayToMatch[$key]) && strpos($arrayToMatch[$key], '@') > - 1) {
                $this->match($value, $arrayToMatch[$key]);

                return true;
            } else {
                return false;
            }

        };

        foreach ($array as $value) {

            if ($match($value, $array, $arrayToMatch)) {
                //unset($array[$key], $arrayToMatch[$key]);
            } else {
                return -1;
            }
        }
    }

    
}