<?php

require_once dirname(__DIR__) . '/bootstrap.php';

/**
 * Tests the PFloat class
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PFloatTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithString()
    {
        $float = new PFloat('3');
        $this->assertInternalType('float', $float->floatValue());
        $this->assertEquals(3.0, $float->floatValue());
    }
    
    public function testConstructWithExponentialString()
    {
        $float = new PFloat('1.3e10');
        $this->assertInternalType('float', $float->floatValue());
        $this->assertEquals(13000000000.0, $float->floatValue());
    }
    
    public function testConstructWithNumber()
    {
        $float = new PFloat(3);
        $this->assertInternalType('float', $float->floatValue());
        $this->assertEquals(3.0, $float->floatValue());
    }
    
    /**
     * ensures pnumbers are converted properly
     */
    public function testConstructWithPNumber()
    {
        $float = new PFloat(new PInteger(3));
        $this->assertInternalType('float', $float->floatValue());
        $this->assertEquals(3.0, $float->floatValue());
    }
    
    public function testValueOfPNumber()
    {
        $float = PFloat::valueOf(new PInteger(3));
        $this->assertInternalType('float', $float->floatValue());
        $this->assertEquals(3.0, $float->floatValue());
    }
    
    public function testEqualsInt()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertTrue($float->equals(3));
    }
    
    public function testEqualsPFloat()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertTrue($float->equals(new PFloat(3)));
    }
    
    public function testEqualsWithGreater()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertFalse($float->equals(4));
    }
    
    public function testEqualsWithLower()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertFalse($float->equals(2.9999999999999));
    }
    
    public function testEqualsWithLowerHighPrecision()
    {
        $float = PFloat::valueOf(         3.0000000000);
        $this->assertFalse($float->equals(2.9999999999, 12));
    }
    
    public function testNotEqualsArray()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertFalse($float->equals(array()));
    }
    
    public function testCompareToIsEqual()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertEquals(PType::COMPARE_EQUALS_OTHER, $float->compareTo(3));
    }
    
    public function testCompare()
    {
        $res = PFloat::compare("3.00", 4.29);
        $this->assertEquals(PType::COMPARE_LESS_THAN_OTHER, $res);
    }
    
    public function testCompareToIsHigher()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertEquals(PType::COMPARE_GREATER_THAN_OTHER, $float->compareTo(2));
    }
    
    public function testCompareToIsLower()
    {
        $float = PFloat::valueOf(3.00);
        $this->assertEquals(PType::COMPARE_LESS_THAN_OTHER, $float->compareTo(4));
    }
    
    public function testParseFloat()
    {
        $float = PFloat::parseFloat("3.00");
        $this->assertEquals(3.0, $float->floatValue());
    }
    
    public function testToHexString()
    {
        $hex = PFloat::toHexString(3.69);
        $this->assertInternalType('string', $hex);
        $this->assertEquals("3", $hex);
    }
    
    public function testToHexString2()
    {
        $hex = PFloat::toHexString(332.29);
        $this->assertInternalType('string', $hex);
        $this->assertEquals("14c", $hex);
    }
    
    public function testHashCode()
    {
        $string = PFloat::valueOf(332.29)->hashCode();
        $this->assertInternalType('string', $string);
        $this->assertEquals("332.29", $string);
    }
    
    public function testByteValueException()
    {
        $this->setExpectedException('BadMethodCallException');
        PFloat::valueOf(3)->byteValue();
    }
    
    public function testFloatToIntBitsException()
    {
        $this->setExpectedException('BadMethodCallException');
        PFloat::valueOf(3)->floatToIntBits(1);
    }
    
    public function testFloatToRawIntBitsException()
    {
        $this->setExpectedException('BadMethodCallException');
        PFloat::valueOf(3)->floatToRawIntBits(1);
    }
    
    public function testIntBitsToFloat()
    {
        $res = PFloat::intBitsToFloat("100010");
        $this->assertInstanceOf('PFloat', $res);
        $this->assertEquals(34.0, $res->floatValue());
    }
    
    public function testInfinite()
    {
        $res = PFloat::isInfinite(INF);
        $this->assertTrue($res);
    }
    
    public function testIsNotInfinite()
    {
        $res = PFloat::isInfinite(PHP_INT_MAX);
        $this->assertFalse($res);
    }
    
    public function testIsNan()
    {
        $res = PFloat::isNaN(NAN);
        $this->assertTrue($res);
    }
    
    public function testIsNotNan()
    {
        $res = PFloat::isNaN(NAN);
        $this->assertTrue($res);
    }
}
