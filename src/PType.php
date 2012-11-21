<?php
/**
 * PType - base class for all types
 */
abstract class PType implements Serializable
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
     * @var string|int|bool|float|double|null
     */
    private $value;

    /**
     * casts the inner value to string
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getInternalValue();
    }

    /**
     * ensures the interval value is a php native type
     * 
     * @param string|int|bool|float|double|null $value
     * @throws LogicException
     */
    protected function setInternalValue($value)
    {
        if (is_object($value)) {
            throw new LogicException('The inner value must not be an object.');
        }
        
        $this->value = $value;
    }
    
    /**
     * returns the internal value
     * @return string|int|bool|float|double|null
     */
    protected function getInternalValue()
    {
        return $this->value;
    }
    
    /**
     * serialization
     * 
     * Returns the inner value.
     * 
     * @return string|int|float|double|null
     */
    public function serialize()
    {
        return $this->getInternalValue();
    }

    /**
     * unserialization
     * 
     * @param mixed $serialized
     * @return void
     * @throws LogicException
     */
    public function unserialize($serialized)
    {
        $this->setInternalValue($serialized);
    }

}
