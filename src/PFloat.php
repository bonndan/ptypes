<?php
/**
 * PFloat
 * 
 * mimics java.lang.Float
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PFloat extends PNumber
{
    /**
     * Pass the initial value to the constructor.
     * 
     * @param mixed $value
     */
    public function __construct($value)
    {
        if ($value instanceof PNumber) {
            $this->setInternalValue($value->floatValue());
            return;
        } elseif (is_float($value)) {
            $this->setInternalValue($value);
            return;
        }
        
        $this->setInternalValue(self::valueOf($value)->floatValue());
    }
    
    /**
     * Returns the value of this Float as a byte (by casting to a byte).
     */
    public function byteValue()
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Compares the two specified float values.
     * 
     * @return int
     */
    public static function compare($float1, $float2, $scale = null)
    {
        if (!is_float($float1)) {
            $float1 = self::valueOf($float1)->floatValue();
        }
        
        if (!is_float($float2)) {
            $float2 = self::valueOf($float2)->floatValue();
        }
        
        if (intval($scale) > 0) {
            return bccomp($float1, $float2, intval($scale));
        }
        
        return bccomp($float1, $float2);
    }

    /**
     * Compares two Float objects numerically.
     * 
     * @return int
     */
    public function compareTo($anotherFloat)
    {
        return self::compare(
            $this->floatValue(),
            self::valueOf($anotherFloat)->floatValue()
        );
    }

    /**
     * Compares this object against the specified object.
     * 
     * @return boolean
     */
    public function equals($obj, $scale = null)
    {
        return self::compare($this->getInternalValue(), $obj, $scale) == self::COMPARE_EQUALS_OTHER;
    }

    /**
     * Returns a representation of the specified floating-point value 
     * according to the IEEE 754 floating-point "single format" bit layout.
     */
    public static function floatToIntBits($value)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns a representation of the specified floating-point value 
     * according to the IEEE 754 floating-point "single format" bit layout,
     * preserving Not-a-Number (NaN) values.
     * 
     * @param float $value
     */
    public static function floatToRawIntBits($value)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns a hash code for this Float object (i.e. the value as string).
     * 
     * @return string
     */
    public function hashCode()
    {
        return $this->__toString();
    }

    /**
     * Returns the float value corresponding to a given bit representation.
     * 
     * @param string $bits
     * @return PFloat
     */
    public static function intBitsToFloat($bits)
    {
        return self::valueOf(bindec($bits));
    }

    /**
     * Returns true if this Float value is infinitely large in magnitude, false otherwise.
     * 
     * @param float|PFloat $float
     * @return boolean
     */
    public static function isInfinite($float)
    {
        return is_infinite($float);
    }

    /**
     * Returns true if this Float value is a Not-a-Number (NaN), false otherwise.
     * 
     * @param float|PFloat $float
     * @return boolean
     */
    public static function isNaN($float)
    {
        return is_nan($float);
    }

    /**
     * Returns a new float initialized to the value represented by the 
     * specified String, as performed by the valueOf method of class PFloat.
     * 
     * @return PFloat
     */
    public static function parseFloat($string)
    {
        return self::valueOf($string);
    }

    /**
     * Returns a hexadecimal string representation of the float argument.
     * 
     * @return string
     */
    public static function toHexString($float)
    {
        return dechex(self::valueOf($float)->floatValue());
    }

    /**
     * Returns a Float instance representing the specified float value.
     * 
     * @param mixed $arg
     * @return PFloat
     */
    public static function valueOf($arg)
    {
        if ($arg instanceof PNumber) {
            return new self($arg->floatValue());
        }
        
        return new self(floatval($arg));
    }

}

