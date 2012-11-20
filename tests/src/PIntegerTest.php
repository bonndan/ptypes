<?php

require_once dirname(__DIR__) . '/bootstrap.php';

/**
 * tests the PInteger class
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PIntegerTest extends PHPUnit_Framework_TestCase
{
    public function testInitWithPNumber()
    {
        $int = new PInteger(new PInteger(123));
        $this->assertEquals(123, $int->intValue());
    }
    
    public function testInitWithString()
    {
        $int = new PInteger('should be null');
        $this->assertEquals(0, $int->intValue());
    }
    
    public function testInitWithIntString()
    {
        $int = new PInteger('123');
        $this->assertEquals(123, $int->intValue());
    }
    
    public function testInitWithInt()
    {
        $int = new PInteger(123);
        $this->assertEquals(123, $int->intValue());
    }
    
    public function testInitWithNonToStringClass()
    {
        $int = new PInteger(new stdClass());
        $this->assertEquals(0, $int->intValue());
    }
    
    public function testInitWithToStringClass()
    {
        $int = new PInteger(new PString('123'));
        $this->assertEquals(123, $int->intValue());
    }
    
    /**
     * ensures an octal is treated as such
     */
    public function testDecodeWithOctal()
    {
        $int = PInteger::decode(042);
        $this->assertEquals(34, $int->intValue());
    }
    
    /**
     * ensures an octal string is treated as octal
     */
    public function testDecodeWithOctalString()
    {
        $int = PInteger::decode('042');
        $this->assertEquals(34, $int->intValue());
    }
    
    public function testParseIntWithString()
    {
        $int = PInteger::parseInt("42");
        $this->assertInstanceOf('PInteger', $int);
        $this->assertEquals(42, $int->intValue());
    }
    
    public function testParseIntWithPString()
    {
        $int = PInteger::parseInt(PString::valueOf("42"));
        $this->assertInstanceOf('PInteger', $int);
        $this->assertEquals(42, $int->intValue());
    }
    
    public function testParseIntThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        PInteger::parseInt(new stdClass());
    }
    
    public function testHashCode()
    {
        $this->assertEquals(42, PInteger::decode(42)->hashCode());
    }
    
    public function testEqualsPInteger()
    {
        $int = new PInteger(42);
        $this->assertTrue($int->equals(new PInteger(42)));
    }
    
    public function testEqualsInt()
    {
        $int = new PInteger(42);
        $this->assertTrue($int->equals(42));
    }
    
    public function testEqualsStringInt()
    {
        $int = new PInteger(42);
        $this->assertTrue($int->equals("42"));
    }
    
    public function testNotEqualsOctal()
    {
        $int = new PInteger(42);
        $this->assertFalse($int->equals("042"));
    }
    
    public function testNotEquals()
    {
        $int = new PInteger(42);
        $this->assertFalse($int->equals(43));
    }
    
    public function testCompareToSmaller()
    {
        $int = new PInteger(42);
        $this->assertEquals(PType::COMPARE_GREATER_THAN_OTHER, $int->compareTo(41));
    }
    
    public function testCompareToGreater()
    {
        $int = new PInteger(42);
        $this->assertEquals(PType::COMPARE_LESS_THAN_OTHER, $int->compareTo(43));
    }
    
    public function testCompareToEquals()
    {
        $int = new PInteger(42);
        $this->assertEquals(PType::COMPARE_EQUALS_OTHER, $int->compareTo(42));
    }
    
    public function testSignumLower()
    {
        $this->assertEquals(PType::COMPARE_LESS_THAN_OTHER, PInteger::signum(-12));
    }
    
    public function testSignumGreater()
    {
        $this->assertEquals(PType::COMPARE_GREATER_THAN_OTHER, PInteger::signum(12));
    }
    
    public function testSignumOfZero()
    {
        $this->assertEquals(PType::COMPARE_EQUALS_OTHER, PInteger::signum(0));
    }
    
    public function testToHexString()
    {
        $hex = PInteger::toHexString(42);
        $this->assertInternalType('string', $hex);
        $this->assertEquals('2a', $hex);
    }
    
    public function testToHexStringWithHex()
    {
        $hex = PInteger::toHexString(0x2a);
        $this->assertInternalType('string', $hex);
        $this->assertEquals('2a', $hex);
    }
    
    public function testToBinString()
    {
        $hex = PInteger::toBinaryString(42);
        $this->assertInternalType('string', $hex);
        $this->assertEquals('101010', $hex);
    }
    
    public function testToOctalString()
    {
        $hex = PInteger::toOctalString(42);
        $this->assertInternalType('string', $hex);
        $this->assertEquals('52', $hex);
    }
    
    public function testToOctalStringWithOctal()
    {
        $hex = PInteger::toOctalString(042);
        $this->assertInternalType('string', $hex);
        $this->assertEquals('42', $hex);
    }
    
    public function testValueOfReturnsPInteger()
    {
        $this->assertInstanceOf('PInteger', PInteger::valueOf(NULL));
        $this->assertInstanceOf('PInteger', PInteger::valueOf(1));
        $this->assertInstanceOf('PInteger', PInteger::valueOf('abc'));
    }
    
    public function testBitCount()
    {
        $this->assertEquals(1, PInteger::bitCount(2));
        $this->assertEquals(3, PInteger::bitCount(42));
    }
    
    public function testHighestOneBitException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::highestOneBit(42);
    }
    
    public function testLowestOneBitException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::lowestOneBit(42);
    }
    
    public function testNumberOfLeadingZerosException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::numberOfLeadingZeros(42);
    }
    
    public function testNumberOfTrailingZerosException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::numberOfTrailingZeros(42);
    }
    
    public function testReverseException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::reverse(42);
    }
    
    public function testReverseBytesException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::reverseBytes(42);
    }
    
    public function testRotateLeftException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::rotateLeft(42, 1);
    }
    
    public function testRotateRightException()
    {
        $this->setExpectedException('BadMethodCallException');
        PInteger::rotateRight(42, 1);
    }
}