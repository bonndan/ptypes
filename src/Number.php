<?php





class PInteger
{
    public function __construct($arg)
    {
        if (!intv)
        $this->_ = (int)$arg;
    }
}

function string($str)
{
    return new PString($str);
}

$string = string('test');
echo $string->length() == strlen($string);
        