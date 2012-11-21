<?php

require_once dirname(__DIR__) . '/bootstrap.php';

/**
 * Tests the PNumber class
 *
 * @author daniel
 */
class PNumberTest extends PHPUnit_Framework_TestCase
{
    public function testImplementsPComparable()
    {
        $this->assertInstanceOf('PComparable', $this->getNumberMock(123));
    }
    
    public function testByteValueException()
    {
        $this->setExpectedException('BadMethodCallException');
        $this->getNumberMock(123)->byteValue();
    }
    
    /**
     * ensures a double is returned
     */
    public function testDoubleValue()
    {
        $mock = $this->getNumberMock(123);
        $this->assertTrue(is_double($mock->doubleValue()));
    }
    
    /**
     * ensures a double (zero) is returned
     */
    public function testDoubleValueFromString()
    {
        $mock = $this->getNumberMock('abc');
        $this->assertTrue(is_double($mock->doubleValue()));
        $this->assertEquals(0, $mock->doubleValue());
    }
    
    /**
     * ensures a float is returned
     */
    public function testFloatValue()
    {
        $mock = $this->getNumberMock(123);
        $this->assertTrue(is_float($mock->floatValue()));
        $this->assertEquals(123, $mock->floatValue());
    }
    
    /**
     * ensures an int is returned
     */
    public function testInt()
    {
        $mock = $this->getNumberMock(124);
        $this->assertTrue(is_int($mock->intValue()));
        $this->assertEquals(124, $mock->intValue());
    }
    
    /**
     * ensures an int is casted from a float
     */
    public function testIntValueFromFloat()
    {
        $mock = $this->getNumberMock(123.2);
        $this->assertTrue(is_int($mock->intValue()));
        $this->assertEquals(123, $mock->intValue());
    }
    
    /**
     * ensures a BadMethodCallException is thrown for byteValue
     */
    public function testByteValueThrowsException()
    {
        $mock = $this->getNumberMock(123.2);
        $this->setExpectedException('BadMethodCallException');
        $mock->byteValue();
    }
    
    /**
     * ensures a BadMethodCallException is thrown for shortValue
     */
    public function testShortThrowsException()
    {
        $mock = $this->getNumberMock(123.2);
        $this->setExpectedException('BadMethodCallException');
        $mock->shortValue();
    }
    
    /**
     * ensures a BadMethodCallException is thrown for longValue
     */
    public function testLongThrowsException()
    {
        $mock = $this->getNumberMock(123.2);
        $this->setExpectedException('BadMethodCallException');
        $mock->longValue();
    }
    
    /**
     * creates a PNumber instance
     * 
     * @param mixed $value
     * @return PNumber
     */
    protected function getNumberMock($value)
    {
        return new PNumberMock($value);
    }
}

class PNumberMock extends PNumber
{
    public function __construct($value)
    {
        $this->setInternalValue($value);
    }
    
    public function compareTo($other)
    {
        //unused
    }

}