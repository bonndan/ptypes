<?php
/**
 * PNumber
 * 
 * Attempts to mimic java.lang.Number, except that short and long conversion
 * are not supported.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @todo Serializable
 */
abstract class PNumber extends PType implements PComparable /*  Serializable */
{
    /**
     * Returns the value of the specified number as a byte.
     * 
     * @throws BadMethodCallException
     */
    public function byteValue()
    {
        throw new BadMethodCallException('Not implemented.');
    }

    /**
     * Returns the value of the specified number as a double.
     * 
     * @return double
     */
    public function doubleValue()
    {
        return doubleval($this->getInternalValue());
    }

    /**
     * Returns the value of the specified number as a float.
     * 
     * @return float
     */
    public function floatValue()
    {
        return floatval($this->getInternalValue());
    }
    
    /**
     * Returns the value of the specified number as an int.
     * 
     * @return int
     */
    public function intValue()
    {
        return intval($this->getInternalValue());
    }
    
    /**
     * Returns the value of the specified number as a long.
     * 
     * @throws BadMethodCallException
     */
    public function longValue()
    {
        throw new BadMethodCallException('Not implemented. Use BCMath.');
    }
    
    /**
     * Returns the value of the specified number as a short.
     * 
     * @throws BadMethodCallException
     */
    public function shortValue()
    {
        throw new BadMethodCallException('Not implemented. Use BCMath.');
    }
}

