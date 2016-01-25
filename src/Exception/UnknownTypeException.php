<?php

namespace Coduo\PHPMatcher\Exception;

class UnknownTypeException extends Exception
{
    private $type;

    public function __construct($type)
    {
        $this->type = "@" . $type . "@";
        parent::__construct(sprintf("Type pattern \"%s\" is not supported.", $this->type), 0, null);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}
