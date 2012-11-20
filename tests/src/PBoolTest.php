<?php

require_once dirname(__DIR__) . '/bootstrap.php';

/**
 * tests the PBool class
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PBoolTest extends PHPUnit_Framework_TestCase
{
    public function testInitWithTrueBool()
    {
        $bool = new PBool(true);
        $this->assertTrue($bool->isTrue());
        $this->assertFalse($bool->isFalse());
    }
    
    public function testInitWithFalseBool()
    {
        $bool = new PBool(false);
        $this->assertFalse($bool->isTrue());
        $this->assertTrue($bool->isFalse());
    }
    
    public function testInitWithPBool()
    {
        $bool = new PBool(new PBool(false));
        $this->assertFalse($bool->isTrue());
        $this->assertTrue($bool->isFalse());
    }
    
    public function testEqualsWithTrueBool()
    {
        $bool = new PBool(true);
        $this->assertTrue($bool->equals(true));
        $this->assertFalse($bool->equals(false));
    }
    
    public function testEqualsWithFalseBool()
    {
        $bool = new PBool(false);
        $this->assertFalse($bool->equals(true));
        $this->assertTrue($bool->equals(false));
    }
    
    public function testValueOfWithTrueBool()
    {
        $bool = PBool::valueOf(true);
        $this->assertInstanceOf('PBool', $bool);
        $this->assertTrue($bool->isTrue());
    }
    
    public function testValueOfWithPBool()
    {
        $original = new PBool();
        $bool = PBool::valueOf($original);
        $this->assertSame($original, $bool);
    }
}