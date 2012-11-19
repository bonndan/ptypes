<?php

require_once dirname(__DIR__) . '/bootstrap.php';

/**
 * tests the charset
 *
 * @author daniel
 */
class PCharsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that create() unifies charset names.
     *
     * If an alias is used to construct the string then the real charset names
     * should be used by the string object afterwards.
     */
    public function testCreateUnifiesCharset()
    {
        $object = new PCharset('latin1');
        $this->assertEquals(PCharset::LATIN1, (string)$object);
    }
    
    /**
     * ensures no-charsets are detected
     */
    public function testEqualsAssertsCharset()
    {
        $charset = new PCharset(PCharset::UTF8);
        $this->setExpectedException('InvalidArgumentException');
        $charset->equals('nonsense');
    }
    
    public function testEqualsReturnsTrueForSameCharset()
    {
        $charset = new PCharset(PCharset::UTF8);
        $result = $charset->equals(PCharset::UTF8);
        $this->assertTrue($result);
    }
    
    public function testEqualsReturnsTrueForAlias()
    {
        $charset = new PCharset(PCharset::LATIN1);
        $result = $charset->equals('latin1');
        $this->assertTrue($result);
    }
    
    public function testEqualsReturnsFalseForOtherCharset()
    {
        $charset = new PCharset(PCharset::UTF8);
        $result = $charset->equals('latin1');
        $this->assertFalse($result);
    }
    
    /**
     * @dataProvider emptyValueProvider
     * @param type $value
     */
    public function testEqualsReturnsFalseForEmptyValues($value)
    {
        $charset = new PCharset(PCharset::UTF8);
        $result = $charset->equals($value);
        $this->assertFalse($result);
    }
    
    public function emptyValueProvider()
    {
        return array(
            array(0),
            array(''),
            array('0'),
            array(null),
            array(false),
        );
    }
    
    public function testAssertStringHasSameCharset()
    {
        $string = 'testäö';
        $charset = new PCharset();
        $this->setExpectedException(null);
        $charset->assertStringHasSameCharset($string);
    }
    
    public function testAssertStringHasSameCharsetThrowsException()
    {
        $string = 'testäö';
        $charset = new PCharset(PCharset::UTF8);
        $this->setExpectedException('InvalidArgumentException');
        $charset->assertStringHasSameCharset(mb_convert_encoding($string, 'UCS-2'));
    }
}

