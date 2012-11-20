<?php

/**
 * Integer representation class.
 * 
 * Cannot be used for calculations, but for type safety.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PInteger extends PNumber
{

    /**
     * The constructor requires a value for initialisation.
     * 
     * PNumber instances a converted using intValue(), objects are casted into
     * string before calling intval(), other values are just intval - ed.
     * 
     * @param PNumber|Object|int|mixed $value
     */
    public function __construct($value)
    {
        if ($value instanceof PNumber) {
            $this->value = $value->intValue();
            return;
        } elseif (is_int($value)) {
            $this->value = $value;
            return;
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = (string) $value;
            } else {
                $value = 0; //fallback to zero
            }
        }

        $this->value = intval($value);
    }

    /**
     * Returns the number of one-bits in the two's complement binary representation
     * of the specified int value.
     * 
     * @return int
     */
    public static function bitCount($int)
    {
        return substr_count(self::toBinaryString($int), "1");
    }

    /**
     * Compares two Integer objects numerically.
     * 
     * @param mixed $anotherInteger
     * 
     * @return string
     */
    public function compareTo($anotherInteger)
    {
        $int = self::decode($anotherInteger)->intValue();
        if ($this->value > $int) {
            return self::COMPARE_GREATER_THAN_OTHER;
        } elseif ($this->value < $int) {
            return self::COMPARE_LESS_THAN_OTHER;
        }

        return self::COMPARE_EQUALS_OTHER;
    }

    /**
     * Decodes a String into an Integer.
     * 
     * Uses intval internally, but handles string-octals as octals and string-exponential
     * notations as non-strings: "042" === 042 and "1e10" === 1e10
     * 
     * @param mixed $arg
     * 
     * @return PInteger
     */
    public static function decode($arg)
    {
        if ($arg instanceof self) {
            return $arg;
        }

        if (is_string($arg)) {
            //octal string
            if ($arg[0] === '0') {
                $arg = octdec($arg);
            } elseif (preg_match('/-?[0-9]*e*[0-9]*/', $arg)) {
                
            }
        }


        return new self(intval($arg));
    }

    /**
     * Compares this object to the specified object.
     * 
     * Returns true if the object has the same intValue.
     * 
     * @param mixed $object
     * @return boolean
     */
    public function equals($object)
    {
        $object = self::decode($object);
        return $object->intValue() == $this->value;
    }

    /**
     * Returns a hash code for this Integer.
     * 
     * For sake of Java API. Returns the internal value as string.
     * 
     * @return string
     */
    public function hashCode()
    {
        return (string) $this->value;
    }

    /**
     * Returns an int value with at most a single one-bit, in the position of the
     * highest-order ("leftmost") one-bit in the specified int value.
     * 
     * @throws BadMethodCallException
     */
    public static function highestOneBit($int)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns an int value with at most a single one-bit, in the position of the l
     * owest-order ("rightmost") one-bit in the specified int value.
     * 
     * @throws BadMethodCallException
     */
    public static function lowestOneBit($int)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns the number of zero bits preceding the highest-order ("leftmost")
     * one-bit in the two's complement binary representation of the specified int value.
     * 
     * @throws BadMethodCallException
     */
    public static function numberOfLeadingZeros($int)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns the number of zero bits following the lowest-order ("rightmost") 
     * one-bit in the two's complement binary representation of the specified int value.
     * 
     * @throws BadMethodCallException
     */
    public static function numberOfTrailingZeros($int)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Parses the string argument as a signed decimal integer.
     * 
     * This method uses intval internally by simply creating a new PInteger.
     * 
     * @param PString|string $string
     * 
     * @return PInteger
     */
    public static function parseInt($string)
    {
        if (!is_string($string) && !$string instanceof PString) {
            throw new InvalidArgumentException('ParseInt expects a string or PString.');
        }

        return new self(intval((string) $string));
    }

    /**
     * Returns the value obtained by reversing the order of the bits in the 
     * two's complement binary representation of the specified int value.
     * 
     * @throws BadMethodCallException
     */
    public static function reverse($int)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns the value obtained by reversing the order of the bytes in the 
     * two's complement representation of the specified int value.
     * 
     * @throws BadMethodCallException
     */
    public static function reverseBytes($int)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns the value obtained by rotating the two's complement binary 
     * representation of the specified int value left by the specified number of bits.
     * 
     * @throws BadMethodCallException
     */
    public static function rotateLeft($int, $distance)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns the value obtained by rotating the two's complement binary 
     * representation of the specified int value right by the specified number of bits.
     * 
     * @throws BadMethodCallException
     */
    public static function rotateRight($int, $distance)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns the signum function of the specified int value.
     * 
     * Turns the argument into PInteger using decode() before examining the signum.
     * 
     * @param int $int 
     * @return int
     */
    public static function signum($int)
    {
        return self::decode($int)->compareTo(0);
    }

    /**
     * Returns a string representation of the integer argument as an unsigned integer in base 2.
     * 
     * @return string
     */
    public static function toBinaryString($int)
    {
        return decbin(self::decode($int)->intValue());
    }

    /**
     * Returns a string representation of the integer argument as an unsigned integer in base 16.
     * 
     * @return string
     */
    public static function toHexString($int)
    {
        return dechex(self::decode($int)->intValue());
    }

    /**
     * Returns a string representation of the integer argument as an unsigned integer in base 8.
     * 
     * @return string
     */
    public static function toOctalString($int)
    {
        return decoct(self::decode($int)->intValue());
    }

    /**
     * Returns a Integer instance representing the specified int value.
     * 
     * @param PType|string|int $arg
     * 
     * @return PInteger
     */
    public static function valueOf($arg)
    {
        return new self($arg);
    }

}
