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
     * ensures the Serializable interface is implemented
     */
    public function testImplementsSerializable()
    {
        $res = $this->getTypeMock(1);
        $this->assertInstanceOf('Serializable', $res);
    }
    
    public function testSerialize()
    {
        $res = $this->getTypeMock(1);
        $this->assertEquals(1, $res->serialize());
    }
    
    public function testUnserialize()
    {
        $res = new PTypeMock(null);
        $res->unserialize("123");
        $this->assertEquals("123", $res->__toString());
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