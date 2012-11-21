<?php

require_once dirname(__DIR__) . '/bootstrap.php';

/**
 * Tests the PType class
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * ensures the __toString method
     */
    public function testToString()
    {
        $mock = $this->getTypeMock('abc');
        $this->assertEquals('abc', $mock->__toString());
    }
    
    /**
     * ensures the internal type is not an object
     */
    public function testInvalidTypeException()
    {
        $this->setExpectedException('LogicException');
        $this->getTypeMock(new stdClass());
    }
    
    
    /**
     * creates a PNumber instance
     * 
     * @param mixed $value
     * @return PType
     */
    protected function getTypeMock($value)
    {
        return new PTypeMock($value);
    }
}

class PTypeMock extends PType
{
    public function __construct($value)
    {
        $this->setInternalValue($value);
    }
}