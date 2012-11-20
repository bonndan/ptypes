<?php
/**
 * PType - base class for all types
 */
abstract class PType
{
    /**
     * Returns value used by compareTo().
     *
     * Indicates that this string is less than the compared one.
     *
     * @var integer
     */
    const COMPARE_LESS_THAN_OTHER = -1;
    
    /**
     * Returns value used by compareTo().
     *
     * Indicates that this string equals the compared one.
     *
     * @var integer
     */
    const COMPARE_EQUALS_OTHER = 0;
    
    /**
     * Returns value used by compareTo().
     *
     * Indicates that this string is greater than the compared one.
     *
     * @var integer
     */
    const COMPARE_GREATER_THAN_OTHER = 1;
    
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
