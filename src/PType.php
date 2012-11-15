<?php
/**
 * PType - base class for all typess
 */
abstract class PType
{
    /**
     * the "native" php value
     * @var mixed
     */
    protected $value;

    /**
     * casts the inner value to string
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

}
