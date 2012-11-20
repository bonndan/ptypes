<?php
/**
 * Bool representation class.
 * 
 * To be used for type safety.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PBool extends PType
{
    /**
     * constructor turns the argument into a boolean. false by default.
     * 
     * @param bool|PBool|mixed $arg
     */
    public function __construct($arg = null)
    {
        if ($arg instanceof self) {
            $this->value = $arg->boolValue();
        } elseif (is_bool($arg)) {
            $this->value = $arg;
        } else {
            $this->value = self::valueOf($arg);
        }
    }
    
    /**
     * check if PBool is true
     * 
     * @return boolean
     */
    public function isTrue()
    {
        return $this->value === true;
    }
    
    /**
     * inverse of isTrue
     * 
     * @return boolean
     */
    public function isFalse()
    {
        return $this->value === false;
    }
    
    /**
     * Turns the argument into a boolean and determintes strict equality.
     * 
     * @param bool|PBool|mixed $arg
     * 
     * @return boolean
     */
    public function equals($arg)
    {
        return $this->value === self::valueOf($arg)->boolValue();
    }
    
    /**
     * returns the boolean representation of the passed argument.
     * 
     * Arguments which are not boolean or PBool are casted to boolean.
     * 
     * @param bool|PBool|mixed $arg
     * @return PBool
     */
    public static function valueOf($arg)
    {
        if ($arg instanceof self) {
            return $arg;
        } else {
            return new self((bool)$arg);
        }
    }
    
    /**
     * returns the boolean value
     * 
     * @return boolean
     */
    public function boolValue()
    {
        return $this->value;
    }
}
