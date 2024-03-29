<?php

require_once dirname(__DIR__) . '/bootstrap.php';

/**
 * PStringTest
 *
 * @category PHP
 * @package Mol_DataType
 * @subpackage Tests
 * @author Matthias Molitor <matthias@matthimatiker.de>
 * @copyright 2012 Matthias Molitor
 * @license http://www.opensource.org/licenses/BSD-3-Clause BSD License
 * @link https://github.com/Matthimatiker/MolComponents
 * @since 16.06.2012
 */

/**
 * Tests the String class.
 *
 * @category PHP
 * @package Mol_DataType
 * @subpackage Tests
 * @author Matthias Molitor <matthias@matthimatiker.de>
 * @copyright 2012 Matthias Molitor
 * @license http://www.opensource.org/licenses/BSD-3-Clause BSD License
 * @link https://github.com/Matthimatiker/MolComponents
 * @since 16.06.2012
 */
class PStringTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * Checks if create() returns a string object.
     */
    public function testCreateReturnsStringObject()
    {
        $object = new PString('test');
        $this->assertStringObject($object);
    }
    
    /**
     * Ensures that create() returns a string with the provided charset.
     */
    public function testCreateReturnsStringWithProvidedCharset()
    {
        $object = new PString('test', PCharset::LATIN1);
        $this->assertStringObject($object);
        $this->assertEquals(PCharset::LATIN1, $object->getCharset());
    }
    
    /**
     * Ensures that create() throws an exception if the provided string does not
     * use the given charset.
     */
    public function testCreateThrowsExceptionIfStringDoesNotUseTheProvidedCharset()
    {
        $this->setExpectedException('InvalidArgumentException');
        $latin1String = iconv(PCharset::UTF8, PCharset::LATIN1, 'täääst');
        new PString($latin1String, PCharset::UTF8);
    }
    
    /**
     * Ensures that create() throws an exception if the provided charset is  not valid.
     */
    public function testCreateThrowsExceptionIfInvalidCharsetIsProvided()
    {
        $this->setExpectedException('InvalidArgumentException');
        new PString('test', 'an-invalid-charset');
    }
    
    /**
     * Checks if create() accepts alias names of charsets.
     */
    public function testCreateSupportsCharsetAliases()
    {
        $this->setExpectedException(null);
        new PString('test', 'latin1');
    }
    
    /**
     * Checks if create() converts the charset of a passed string object if necessary.
     */
    public function testCreateConvertsCharsetOfProvidedStringObjectIfNecessary()
    {
        $input  = $this->create('test', PCharset::LATIN1);
        $object = new PString($input, PCharset::UTF8);
        $this->assertStringObject($object);
        $this->assertEquals(PCharset::UTF8, $object->getCharset());
    }
    
    /**
     * Checks if toString() returns a string.
     */
    public function testToStringReturnsString()
    {
        $object = $this->create('test');
        $this->assertInternalType('string', $object->toString());
    }
    
    /**
     * Checks if toString() returns the correct string.
     */
    public function testToStringReturnsCorrectValue()
    {
        $object = $this->create('test');
        $this->assertInternalType('string', $object->toString());
        $this->assertEquals('test', $object->toString());
    }
    
    /**
     * Checks if convertTo() returns a string with the requested charset.
     */
    public function testConvertToReturnsStringWithProvidedCharset()
    {
        $object    = $this->create('test');
        $converted = $object->convertTo(PCharset::LATIN1);
        $this->assertStringObject($converted);
        $this->assertEquals(PCharset::LATIN1, $converted->getCharset());
    }
    
    /**
     * Ensures convertTo does not return copies
     */
    public function testConvertToReturnsSameObject()
    {
        $object    = $this->create('test');
        $converted = $object->convertTo(PCharset::LATIN1);
        $this->assertSame($object, $converted);
    }
    
    /**
     * Checks if convertTo() converts the string into the requested charset.
     */
    public function testConvertChangesCharsetOfOriginalString()
    {
        $object    = $this->create('tääst');
        $converted = $object->convertTo(PCharset::LATIN1);
        $this->assertStringObject($converted);
        $this->assertNotEquals('tääst', $converted->toString());
    }
    
    /**
     * Ensures that convertTo() returns the original string object if the
     * current charset is requested.
     */
    public function testConvertToReturnsSelfIfCurrentCharsetIsRequested()
    {
        $object    = $this->create('tääst');
        $converted = $object->convertTo(PCharset::UTF8);
        $this->assertSame($object, $converted);
    }
    
    /**
     * Checks if convertTo() uses character transliteration (using similar
     * characters if the characters cannot be converted to the requested
     * charset).
     */
    public function testConvertToUsesCharacterTransliteration()
    {
        $object = $this->create('one test for 10€');
        // The € sign is not available in Latin1.
        $converted = $object->convertTo(PCharset::LATIN1);
        $this->assertStringObject($converted);
        // The string must not end with "10", otherwise the € sign was silently discarded.
        $this->assertStringEndsNotWith('10', $converted->toString());
    }
    
    /**
     * Ensures that convertTo() throws an exception if an invalid charset is passed.
     */
    public function testConvertToThrowsExceptionIfInvalidCharsetIsRequested()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->create('test')->convertTo('an-invalid-charset');
    }
    
    /**
     * Ensures that inexOf() returns -1 if the string does not contain
     * the needle.
     */
    public function testIndexOfReturnsMinusOneIfStringDoesNotContainNeedle()
    {
        $index = $this->create('abcabc')->indexOf('d');
        $this->assertEquals(-1, $index);
    }
    
    /**
     * Ensures that indexOf() returns -1 if the haystack string is empty.
     */
    public function testIndexOfReturnsMinusOneIfStringIsEmpty()
    {
        $index = $this->create('')->indexOf('d');
        $this->assertEquals(-1, $index);
    }
    
    /**
     * Ensures that indexOf() returns -1 if the provided start index exceeds
     * the length of the string.
     */
    public function testIndexOfReturnsMinusOneIfOffsetExceedsStringLength()
    {
        $index = $this->create('abc')->indexOf('c', 3);
        $this->assertEquals(-1, $index);
    }
    
    /**
     * Checks if indexOf() returns the correct index.
     */
    public function testIndexOfReturnsCorrectIndex()
    {
        $index = $this->create('abcabc')->indexOf('c');
        $this->assertEquals(2, $index);
    }
    
    /**
     * Ensures that indexOf() returns the correct index even if the string contains
     * multi-byte chracters.
     */
    public function testIndexOfReturnsCorrectIndexIfStringContainsMultiByteCharacters()
    {
        $index = $this->create('äbcäbc')->indexOf('c');
        $this->assertEquals(2, $index);
    }
    
    /**
     * Ensures that indexOf() starts to search at the provided offset.
     */
    public function testIndexOfDoesNotSearchBeforeProvidedOffset()
    {
        $index = $this->create('abcabc')->indexOf('a', 1);
        $this->assertEquals(3, $index);
    }
    
    /**
     * Checks if indexOf() accepts a string object as argument.
     */
    public function testIndexOfAcceptsStringObjectAsArgument()
    {
        $index = $this->create('abcabc')->indexOf($this->create('b'));
        $this->assertEquals(1, $index);
    }
    
    /**
     * Ensures that lastIndexOf() returns -1 of the string does not contain the needle.
     */
    public function testLastIndexOfReturnsMinusOneIfStringDoesNotContainNeedle()
    {
        $index = $this->create('abcabc')->lastIndexOf('d');
        $this->assertEquals(-1, $index);
    }
    
    /**
     * Checks if lastIndexOf() returns the correct index.
     */
    public function testLastIndexOfReturnsCorrectIndex()
    {
        $index = $this->create('abcabc')->lastIndexOf('b');
        $this->assertEquals(4, $index);
    }
    
    /**
     * Ensures that lastIndexOf() returns the correct index even if the string contains
     * multi-byte chracters.
     */
    public function testLastIndexOfReturnsCorrectIndexIfStringContainsMultibyteCharacters()
    {
        $index = $this->create('äbcäbc')->lastIndexOf('b');
        $this->assertEquals(4, $index);
    }
    
    /**
     * Ensures that lastIndexOf() does not search after the provided offset.
     */
    public function testLastIndexOfDoesNotSearchAfterProvidedOffset()
    {
        $index = $this->create('abcabc')->lastIndexOf('b', 3);
        $this->assertEquals(1, $index);
    }
    
    /**
     * Ensures that searching via lastIndexOf() includes the character
     * at the provided $fromIndex position.
     */
    public function testLastIndexOfIncludesCharacterAtFromIndex()
    {
        $index = $this->create('abcabc')->lastIndexOf('a', 3);
        $this->assertEquals(3, $index);
    }
    
    /**
     * Checks if lastIndexOf() accepts a string object as argument.
     */
    public function testLastIndexOfAcceptsStringObjectAsArgument()
    {
        $index = $this->create('abcabc')->lastIndexOf($this->create('b'));
        $this->assertEquals(4, $index);
    }
    
    /**
     * Checks if indexesOf() returns an array.
     */
    public function testIndexesOfReturnsArray()
    {
        $indexes = $this->create('abcabc')->indexesOf('a');
        $this->assertInternalType('array', $indexes);
    }
    
    /**
     * Ensures that indexesOf() returns an array that contains the correct indexes.
     */
    public function testIndexesOfReturnsCorrectIndexes()
    {
        $indexes = $this->create('abcabc')->indexesOf('a');
        $this->assertInternalType('array', $indexes);
        $this->assertContains(0, $indexes);
        $this->assertContains(3, $indexes);
    }
    
    /**
     * Ensures that indexesOf() returns the correct indexes even if the string contains
     * multibyte characters.
     */
    public function testIndexesOfReturnsCorrectIndexesIfStringContainsMultibyteCharacters()
    {
        $indexes = $this->create('äbcäbc')->indexesOf('b');
        $this->assertInternalType('array', $indexes);
        $this->assertContains(1, $indexes);
        $this->assertContains(4, $indexes);
    }
    
    /**
     * Checks if indexesOf() returns the expected number of indexes.
     */
    public function testIndexesOfReturnsCorrectNumberOfIndexes()
    {
        $indexes = $this->create('abcabc')->indexesOf('a');
        $this->assertInternalType('array', $indexes);
        $this->assertEquals(2, count($indexes));
    }
    
    /**
     * Checks if the result of indexesOf() is a sorted integer array.
     */
    public function testIndexesOfReturnsSortedIndexes()
    {
        $indexes = $this->create('abcabc')->indexesOf('a');
        $this->assertInternalType('array', $indexes);
        $sorted = $indexes;
        sort($sorted);
        $this->assertEquals($sorted, $indexes);
    }
    
    /**
     * Checks if indexesOf() accepts a string object as argument.
     */
    public function testIndexesOfAcceptsStringObjectAsArgument()
    {
        $indexes = $this->create('abc')->indexesOf($this->create('b'));
        $this->assertInternalType('array', $indexes);
        $this->assertContains(1, $indexes);
    }
    
    /**
     * Ensures that startsWith() returns true if the string starts with the given
     * prefix.
     */
    public function testStartsWithReturnsTrueIfTheStringStartsWithTheProvidedPrefix()
    {
        $result = $this->create('this is a test string')->startsWith('this');
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that startsWith() returns false if the string does not start with
     * the prefix but contains it.
     */
    public function testStartsWithReturnsFalseIfTheStringOnlyContainsThePrefix()
    {
        $result = $this->create('this is a test string')->startsWith('test');
        $this->assertFalse($result);
    }
    
    /**
     * Ensures that startsWith() returns false if the string does not even contain the prefix.
     */
    public function testStartsWithReturnsFalseIfTheStringDoesNotContainThePrefix()
    {
        $result = $this->create('this is a test string')->startsWith('demo');
        $this->assertFalse($result);
    }
    
    /**
     * Ensures that startsWith() returns false if the string is shorter than the
     * prefix and equals the first part of the prefix.
     */
    public function testStartsWithReturnsFalseIfStringEqualsFirstPartOfPrefix()
    {
        $result = $this->create('test')->startsWith('testprefix');
        $this->assertFalse($result);
    }
    
    /**
     * Checks if startsWith() accepts a string object as argument.
     */
    public function testStartsWithAcceptsStringObjectAsArgument()
    {
        $result = $this->create('test-string')->startsWith($this->create('test'));
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that endsWith() returns true if the string ends with the given suffix.
     */
    public function testEndsWithReturnsTrueIfTheStringEndsWithTheProvidedSuffix()
    {
        $result = $this->create('this is a test string')->endsWith('string');
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that endsWith() returns false if the string does not end with the
     * given suffix but contains it.
     */
    public function testEndsWithReturnsFalseIfTheStringOnlyContainsTheSuffix()
    {
        $result = $this->create('this is a test string')->endsWith('test');
        $this->assertFalse($result);
    }
    
    /**
     * Ensures that endsWith() returns false if the string does not even contain
     * the given suffix.
     */
    public function testEndsWithReturnsFalseIfTheStringDoesNotContainTheSuffix()
    {
        $result = $this->create('this is a test string')->endsWith('demo');
        $this->assertFalse($result);
    }
    
    /**
     * Checks if endsWith() accepts a string object as argument.
     */
    public function testEndsWithAcceptsStringObjectAsArgument()
    {
        $result = $this->create('test-string')->endsWith($this->create('string'));
        $this->assertTrue($result);
    }
    
    /**
     * Checks if removePrefix() removes the given prefix from the string.
     */
    public function testRemovePrefixRemovesProvidedPrefix()
    {
        $object = $this->create('this is a test string')->removePrefix('this ');
        $this->assertStringObject($object);
        $this->assertEquals('is a test string', $object->toString());
    }
    
    /**
     * Ensures that removePrefix() removes the prefix only once.
     */
    public function testRemovePrefixRemovesPrefixOnlyOnce()
    {
        $object = $this->create('testtestdemo')->removePrefix('test');
        $this->assertStringObject($object);
        $this->assertEquals('testdemo', $object->toString());
    }
    
    /**
     * Ensures that removePrefix() does not modify the string if it does not
     * start with the prefix but contains it.
     */
    public function testRemovePrefixDoesNotModifyStringIfItOnlyContainsPrefix()
    {
        $object = $this->create('this is a test string')->removePrefix('test');
        $this->assertStringObject($object);
        $this->assertEquals('this is a test string', $object->toString());
    }
    
    /**
     * Checks if removePrefix() accepts a string object as argument.
     */
    public function testRemovePrefixAcceptsStringObjectAsArgument()
    {
        $object = $this->create('this is a test string')->removePrefix($this->create('this '));
        $this->assertStringObject($object);
        $this->assertEquals('is a test string', $object->toString());
    }
    
    /**
     * Checks if removeSuffix() removes the provided suffix from the string.
     */
    public function testRemoveSuffixRemovesProvidedSuffix()
    {
        $object = $this->create('this is a test string')->removeSuffix(' string');
        $this->assertStringObject($object);
        $this->assertEquals('this is a test', $object->toString());
    }
    
    /**
     * Ensures that removeSuffix() removes the suffix only once.
     */
    public function testRemoveSuffixRemovesSuffixOnlyOnce()
    {
        $object = $this->create('demotesttest')->removeSuffix('test');
        $this->assertStringObject($object);
        $this->assertEquals('demotest', $object->toString());
    }
    
    /**
     * Ensures that removeSuffix() does not modify the string if it does not
     * end with the suffix but contains it.
     */
    public function testRemoveSuffixDoesNotModifyStringIfItOnlyContainsSuffix()
    {
        $object = $this->create('this is a test string')->removeSuffix('test');
        $this->assertStringObject($object);
        $this->assertEquals('this is a test string', $object->toString());
    }
    
    /**
     * Checks if removeSuffix() accepts a string object as argument.
     */
    public function testRemoveSuffixAcceptsStringObjectAsArgument()
    {
        $object = $this->create('this is a test string')->removeSuffix($this->create(' string'));
        $this->assertStringObject($object);
        $this->assertEquals('this is a test', $object->toString());
    }
    
    /**
     * Ensures that replace() does not modify the string if it does not contain
     * the search value.
     */
    public function testReplaceDoesNotModifyStringIfItDoesNotContainSearchString()
    {
        $object = $this->create('hello world')->replace('foo', 'bar');
        $this->assertStringObject($object);
        $this->assertEquals('hello world', $object->toString());
    }
    
    /**
     * Tests signature replace(string, string):
     * Checks if replace() replaces the search string by the provided values.
     */
    public function testReplaceReplacesSingleSearchStringByReplaceValue()
    {
        $object = $this->create('hello world')->replace('hello', 'bye');
        $this->assertStringObject($object);
        $this->assertEquals('bye world', $object->toString());
    }
    
    /**
     * Tests signature replace(array(string), string):
     * Checks if replace() replaces all search strings by the provided value.
     */
    public function testReplaceReplacesListOfSearchStringsByReplaceValue()
    {
        $object = $this->create('hello world')->replace(array('hello', 'world'), 'dummy');
        $this->assertStringObject($object);
        $this->assertEquals('dummy dummy', $object->toString());
    }
    
    /**
     * Tests signature replace(array(string=>string)):
     * Checks if replace() applies the mapping of search/replace pairs to the string.
     */
    public function testReplaceAppliesMappingIfAssociativeArrayIsProvided()
    {
        $mapping = array(
            'hello' => 'welcome',
            'world' => 'home'
        );
        $object  = $this->create('hello world')->replace($mapping);
        $this->assertStringObject($object);
        $this->assertEquals('welcome home', $object->toString());
    }
    
    /**
     * Checks if replace() accepts a string object as search parameter.
     */
    public function testReplaceAcceptsStringObjectAsSearchParameter()
    {
        $object = $this->create('hello world')->replace($this->create('hello'), 'dummy');
        $this->assertStringObject($object);
        $this->assertEquals('dummy world', $object->toString());
    }
    
    /**
     * Ensures that replace() accepts an array of string objects as search parameter.
     */
    public function testReplaceAcceptsCollectionOfStringObjectsAsSearchParameter()
    {
        $search = array(
            $this->create('hello'),
            $this->create('home')
        );
        $object = $this->create('hello world')->replace($search, 'dummy');
        $this->assertStringObject($object);
        $this->assertEquals('dummy world', $object->toString());
    }
    
    /**
     * Checks if replace() accepts a string object as replace parameter.
     */
    public function testReplaceAcceptsStringObjectAsReplaceParamater()
    {
        $object = $this->create('hello world')->replace('hello', $this->create('dummy'));
        $this->assertStringObject($object);
        $this->assertEquals('dummy world', $object->toString());
    }
    
    /**
     * Checks if subString() extracts the correct part of the string.
     */
    public function testSubStringExtractsRequestedPartOfString()
    {
        $subString = $this->create('the brown dog digs')->subString(4, 5);
        $this->assertStringObject($subString);
        $this->assertEquals('brown', $subString->toString());
    }
    
    /**
     * Ensures that the stubString() is extended to end of the original string if no
     * length parameter is provided.
     */
    public function testSubStringExtendsSubStringToEndOfOriginalStringIfLengthIsNotProvided()
    {
        $subString = $this->create('the brown dog digs')->subString(10);
        $this->assertStringObject($subString);
        $this->assertEquals('dog digs', $subString->toString());
    }
    
    /**
     * Ensures that the stubString() is extended to end of the original string if the provided
     * length exceeds the length of the original string.
     */
    public function testSubStringExtendsSubStringToEndOfOriginalStringIfLengthExceedsOriginalString()
    {
        $subString = $this->create('the brown dog digs')->subString(10, 20);
        $this->assertStringObject($subString);
        $this->assertEquals('dog digs', $subString->toString());
    }
    
    /**
     * Checks if subString() handles multi-byte characters (for example umlauts) correctly.
     */
    public function testSubStringWorksWithUmlauts()
    {
        $subString = $this->create('täst täst')->subString(5);
        $this->assertStringObject($subString);
        $this->assertEquals('täst', $subString->toString());
    }
    
    /**
     * Checks if toUpperCase() returns the correct string.
     */
    public function testToUpperCaseReturnsCorrectValue()
    {
        $object = $this->create('aBc')->toUpperCase();
        $this->assertStringObject($object);
        $this->assertEquals('ABC', $object->toString());
    }
    
    /**
     * Checks if toUpperCase() treats umlauts correctly.
     */
    public function testToUpperCaseWorksWithUmlauts()
    {
        $object = $this->create('äÖü')->toUpperCase();
        $this->assertStringObject($object);
        $this->assertEquals('ÄÖÜ', $object->toString());
    }
    
    /**
     * Checks if toLowerCase() returns the correct string.
     */
    public function testToLowerCaseReturnsCorrectValue()
    {
        $object = $this->create('AbC')->toLowerCase();
        $this->assertStringObject($object);
        $this->assertEquals('abc', $object->toString());
    }
    
    /**
     * Checks if toLowerCase() treats umlauts correctly.
     */
    public function testToLowerCaseWorksWithUmlauts()
    {
        $object = $this->create('ÄöÜ')->toLowerCase();
        $this->assertStringObject($object);
        $this->assertEquals('äöü', $object->toString());
    }
    
    /**
     * Checks if trim() removes whitespace from the start of the string.
     */
    public function testTrimRemovesWhitespaceFromStart()
    {
        $object = $this->create(' abc')->trim();
        $this->assertStringObject($object);
        $this->assertEquals('abc', $object->toString());
    }
    
    /**
     * Checks if trim() removes whitespace from the end of the string.
     */
    public function testTrimRemovesWhitespaceFromEnd()
    {
        $object = $this->create('abc ')->trim();
        $this->assertStringObject($object);
        $this->assertEquals('abc', $object->toString());
    }
    
    /**
     * Checks if trim() removes the provided characters from the start of the string.
     */
    public function testTrimRemovesProvidedCharactersFromStart()
    {
        $object = $this->create('abc')->trim('ba');
        $this->assertStringObject($object);
        $this->assertEquals('c', $object->toString());
    }
    
    /**
     * Checks if trim() removes the provided characters from the end of the string.
     */
    public function testTrimRemovesProvidedCharactersFromEnd()
    {
        $object = $this->create('abc')->trim('cb');
        $this->assertStringObject($object);
        $this->assertEquals('a', $object->toString());
    }
    
    /**
     * Checks if trimLeft() removes whitespace from the start of the string.
     */
    public function testTrimLeftRemovesWhitespaceFromStart()
    {
        $object = $this->create(' abc')->trimLeft();
        $this->assertStringObject($object);
        $this->assertEquals('abc', $object->toString());
    }
    
    /**
     * Ensures that trimLeft() does not touch whitespace at the end of the string.
     */
    public function testTrimLeftDoesNotTouchWhitespaceAtTheEndOfTheString()
    {
        $object = $this->create('abc ')->trimLeft();
        $this->assertStringObject($object);
        $this->assertEquals('abc ', $object->toString());
    }
    
    /**
     * Checks if trimLeft() removes the provided characters from the start of the string.
     */
    public function testTrimLeftRemovesProvidedCharactersFromStart()
    {
        $object = $this->create('abc')->trimLeft('ba');
        $this->assertStringObject($object);
        $this->assertEquals('c', $object->toString());
    }
    
    /**
     * Ensures that trimLeft() does not touch the characters at the end of the string.
     */
    public function testTrimLeftDoesNotTouchProvidedCharactersAtTheEndOfTheString()
    {
        $object = $this->create('abc')->trimLeft('c');
        $this->assertStringObject($object);
        $this->assertEquals('abc', $object->toString());
    }
    
    /**
     * Checks if trimRight() removes whitespace from the end of the string.
     */
    public function testTrimRightRemovesWhitespaceFromEnd()
    {
        $object = $this->create('abc ')->trimRight();
        $this->assertStringObject($object);
        $this->assertEquals('abc', $object->toString());
    }
    
    /**
     * Ensures that trimRight() does not touch whitespace at the start of the string.
     */
    public function testTrimRightDoesNotTouchWhitespaceAtTheStartOfTheString()
    {
        $object = $this->create(' abc')->trimRight();
        $this->assertStringObject($object);
        $this->assertEquals(' abc', $object->toString());
    }
    
    /**
     * Checks if trimRight() removes the provided characters from the end of the string.
     */
    public function testTrimRightRemovesProvidedCharactersFromEnd()
    {
        $object = $this->create('abc')->trimRight('cb');
        $this->assertStringObject($object);
        $this->assertEquals('a', $object->toString());
    }
    
    /**
     * Ensures that trimRight() does not touch characters at the start of the string.
     */
    public function testTrimRightDoesNotTouchProvidedCharactersAtTheStartOfTheString()
    {
        $object = $this->create('abc')->trimRight('a');
        $this->assertStringObject($object);
        $this->assertEquals('abc', $object->toString());
    }
    
    /**
     * Checks if toCharArray() returns an array.
     */
    public function testToCharactersReturnsArray()
    {
        $characters = $this->create('abcde')->toCharArray();
        $this->assertInternalType('array', $characters);
    }
    
    /**
     * Checks if toCharArray() returns the expected number of characters.
     */
    public function testToCharactersReturnsExpectedNumberOfCharacters()
    {
        $characters = $this->create('abcde')->toCharArray();
        $this->assertInternalType('array', $characters);
        $this->assertEquals(5, count($characters));
    }
    
    /**
     * Ensures that toCharArray() returns the correct characters.
     */
    public function testToCharactersReturnsCorrectCharacters()
    {
        $characters = $this->create('abcde')->toCharArray();
        $this->assertInternalType('array', $characters);
        $this->assertContains('a', $characters);
        $this->assertContains('b', $characters);
        $this->assertContains('c', $characters);
        $this->assertContains('d', $characters);
        $this->assertContains('e', $characters);
    }
    
    /**
     * Checks if toCharArray() returns the characters in correct order.
     */
    public function testToCharactersReturnsCharactersInCorrectOrder()
    {
        $characters = $this->create('edcba')->toCharArray();
        $this->assertInternalType('array', $characters);
        $expected = array(
            'e',
            'd',
            'c',
            'b',
            'a'
        );
        $this->assertEquals($expected, $characters);
    }
    
    /**
     * Checks if toCharArray() handles multi-byte characters (for example
     * umlauts) correctly.
     */
    public function testToCharactersWorksWithUmlauts()
    {
        $characters = $this->create('äbcü')->toCharArray();
        $this->assertInternalType('array', $characters);
        $expected = array(
            'ä',
            'b',
            'c',
            'ü'
        );
        $this->assertEquals($expected, $characters);
    }
    
    /**
     * Checks if the string object is traverable.
     */
    public function testStringIsTraversable()
    {
        $object = $this->create('test');
        $this->assertInstanceOf('Traversable', $object);
    }
    
    /**
     * Ensures that getIterator() returns an instance of Traversable.
     */
    public function testGetIteratorReturnsTraversable()
    {
        $iterator = $this->create('test')->getIterator();
        $this->assertInstanceOf('Traversable', $iterator);
    }
    
    /**
     * Checks if the iteration loops through the characters of the string.
     */
    public function testIterationLoopsThroughCharacters()
    {
        $object     = $this->create('abc');
        $characters = array();
        foreach ($object as $character) {
            /* @var $character string */
            $this->assertInternalType('string', $character);
            $characters[] = $character;
        }
        $this->assertEquals('abc', implode('', $characters));
    }
    
    /**
     * Ensures that equals() returns true if the string are equal.
     */
    public function testEqualsReturnsTrueIfStringsAreEqual()
    {
        $equal = $this->create('abc')->equals('abc');
        $this->assertTrue($equal);
    }
    
    /**
     * Ensures that equals() returns false if the compared strings
     * have different lengths.
     */
    public function testEqualsReturnsFalseIfStringLengthIsNotEqual()
    {
        $equal = $this->create('abcde')->equals('abc');
        $this->assertFalse($equal);
    }
    
    /**
     * Ensures that equals() returns false if teh compared string have the
     * same length, but their content differs.
     */
    public function testEqualsReturnsFalseIfStringContentDiffers()
    {
        $equal = $this->create('abc')->equals('cba');
        $this->assertFalse($equal);
    }
    
    /**
     * Checks if equals() accepts string objects as argument.
     */
    public function testEqualsAcceptsStringObjectAsArgument()
    {
        $equal = $this->create('abc')->equals($this->create('abc'));
        $this->assertTrue($equal);
    }
    
    /**
     * Ensures that equals() converts the charset of a provided string
     * object before comparison.
     */
    public function testEqualsUnifiesCharsetToCompareStrings()
    {
        $latin1 = $this->create('äüö')->convertTo(PCharset::LATIN1);
        $this->assertStringObject($latin1);
        // The raw string values are not equal, but equals() should convert
        // the charset of the provided string for comparison.
        $equal = $this->create('äüö')->equals($latin1);
        $this->assertTrue($equal);
    }
    
    /**
     * Checks if length() returns an integer.
     */
    public function testLengthReturnsInteger()
    {
        $length = $this->create('abcde')->length();
        $this->assertInternalType('integer', $length);
    }
    
    /**
     * Checks if length() returns the number of characters.
     */
    public function testLengthReturnsCorrectValue()
    {
        $length = $this->create('abcde')->length();
        $this->assertEquals(5, $length);
    }
    
    /**
     * Ensures that length() returns the correct number of characters even
     * if the string contains multi-byte characters.
     */
    public function testLengthReturnsCorrectValueIfStringContainsUmlauts()
    {
        $length = $this->create('äbcöü')->length();
        $this->assertEquals(5, $length);
    }
    
    /**
     * Ensures that lengthInBytes() returns an integer.
     */
    public function testLengthInBytesReturnsInteger()
    {
        $bytes = $this->create('abc')->lengthInBytes();
        $this->assertInternalType('integer', $bytes);
    }
    
    /**
     * Checks if lengthInBytes() returns the correct number of bytes.
     */
    public function testLengthInBytesReturnsCorrectValue()
    {
        $bytes = $this->create('abc')->lengthInBytes();
        $this->assertEquals(3, $bytes);
    }
    
    /**
     * Ensures that lengthInBytes() returns the correct value if the string
     * contains multi-byte characters.
     */
    public function testLengthInBytesReturnsCorrectValueIfStringContainsUmlauts()
    {
        $bytes = $this->create('äbc')->lengthInBytes();
        $this->assertEquals(4, $bytes);
    }
    
    /**
     * Checks if the string is countable.
     */
    public function testStringIsCountable()
    {
        $object = $this->create('abc');
        $this->assertInstanceOf('Countable', $object);
    }
    
    /**
     * Ensures that count() returns the same value as length().
     */
    public function testCountReturnsSameValueAsLength()
    {
        $object = $this->create('äbc');
        $this->assertEquals($object->length(), $object->count());
    }
    
    /**
     * Ensures that isEmpty() returns true if the length of the string is 0.
     */
    public function testIsEmptyReturnsTrueIfStringLengthIsZero()
    {
        $empty = $this->create('')->isEmpty();
        $this->assertTrue($empty);
    }
    
    /**
     * Ensures that isEmpty() returns false if the string contains only whitespace.
     */
    public function testIsEmptyReturnsFalseIfStringContainsOnlyWhitespace()
    {
        $empty = $this->create('   ')->isEmpty();
        $this->assertFalse($empty);
    }
    
    /**
     * Ensures that isEmpty() returns false if the string contains non-whitespace
     * characters.
     */
    public function testIsEmptyReturnsFalseIfStringContainsNonWhitespaceCharacters()
    {
        $empty = $this->create('abc ')->isEmpty();
        $this->assertFalse($empty);
    }
    
    /**
     * Checks if it is possible to cast the object to a string.
     */
    public function testCastingObjectToStringReturnsCorrectValue()
    {
        $object = $this->create('abc');
        $this->assertEquals($object->toString(), (string)$object);
    }
    
    /**
     * Ensures that contains() returns false if the string does not contain
     * the needle.
     */
    public function testContainsReturnsFalseIfStringDoesNotContainNeedle()
    {
        $result = $this->create('abc')->contains('d');
        $this->assertFalse($result);
    }
    
    /**
     * Ensures that contains() returns true if the string contains the needle.
     */
    public function testContainsReturnsTrueIfStringContainsNeedle()
    {
        $result = $this->create('abc')->contains('b');
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that contains() returns true if string and needle are equal.
     */
    public function testContainsReturnsTrueIfStringEqualsNeedle()
    {
        $result = $this->create('abc')->contains('abc');
        $this->assertTrue($result);
    }
    
    /**
     * Checks if contains() accepts a string object as argument.
     */
    public function testContainsAcceptsStringObjectAsArgument()
    {
        $result = $this->create('abc')->contains($this->create('b'));
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that containsAny() returns true if a list of needles is provided and
     * the string contains at least one needle in the list.
     */
    public function testContainsAnyReturnsTrueIfStringContainsAtLeastOneOfTheNeedles()
    {
        $result = $this->create('abc')->containsAny(array('d', 'a'));
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that containsAny() returns false if a list of needles is provided
     * and the string does not contain any of the needles.
     */
    public function testContainsAnyReturnsFalseIfStringContainsNoneOfTheNeedles()
    {
        $result = $this->create('abc')->containsAny(array('d', 'f'));
        $this->assertFalse($result);
    }
    
    /**
     * Ensures that containsAny() can work with string objects.
     */
    public function testContainsAnyAcceptsCollectionOfStringObjectsAsArgument()
    {
        $result = $this->create('abc')->containsAny(array($this->create('b')));
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that containsAny() returns true if an empty list of needles is provided.
     */
    public function testContainsAnyReturnsTrueIfListOfNeedlesIsEmpty()
    {
        $result = $this->create('abc')->containsAny(array());
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that containsAll() returns true if the string contains all
     * of the provided needles.
     */
    public function testContainsAllReturnsTrueIfStringContainsAllNeedles()
    {
        $result = $this->create('abc')->containsAll(array('a', 'c'));
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that containsAll() returns false if the string contains only some
     * of the provided needles.
     */
    public function testContainsAllReturnsFalseIfStringContainsOnlySomeOfTheNeedles()
    {
        $result = $this->create('abc')->containsAll(array('a', 'd', 'c'));
        $this->assertFalse($result);
    }
    
    /**
     * Checks if containsAll() accepts a list of string objects as argument.
     */
    public function testContainsAllAcceptsCollectionOfStringObjectsAsArgument()
    {
        $needles = array($this->create('b'), $this->create('c'));
        $result  = $this->create('abc')->containsAll($needles);
        $this->assertTrue($result);
    }
    
    /**
     * Ensures that containsAll() returns true if an empty list of needles is provided.
     */
    public function testContainsAllReturnsTrueIfListOfNeedlesIsEmpty()
    {
        $result = $this->create('abc')->containsAll(array());
        $this->assertTrue($result);
    }
    
    /**
     * Checks if reverse() inverts the order of characters.
     */
    public function testReverseInvertsCharacterOrder()
    {
        $inverted = $this->create('abc')->reverse();
        $this->assertStringObject($inverted);
        $this->assertEquals('cba', $inverted->toString());
    }
    
    /**
     * Checks if reverse() can handle multi-byte characters.
     */
    public function testReverseSupportsMultiByteCharacters()
    {
        $inverted = $this->create('äöü')->reverse();
        $this->assertStringObject($inverted);
        $this->assertEquals('üöä', $inverted->toString());
    }
    
    /**
     * Ensures reverse returns the original object
     */
    public function testReverseReturnsSameString()
    {
        $object = $this->create('äöü');
        $inverted = $object->reverse();
        $this->assertSame($object, $inverted);
    }
    
    /**
     * Checks if splitAt() returns an array.
     */
    public function testSplitAtReturnsArray()
    {
        $parts = $this->create('hello splitted world')->splitAt(' ');
        $this->assertInternalType('array', $parts);
    }
    
    /**
     * Ensures that splitAt() returns the correct parts of the string.
     */
    public function testSplitAtReturnsCorrectPartsOfString()
    {
        $parts = $this->create('hello splitted world')->splitAt(' ');
        $this->assertInternalType('array', $parts);
        $this->assertContains('hello', $parts);
        $this->assertContains('splitted', $parts);
        $this->assertContains('world', $parts);
    }
    
    /**
     * Checks if splitAt() returns the expected number of string parts.
     */
    public function testSplitAtReturnsExpectedNumberOfParts()
    {
        $parts = $this->create('hello splitted world')->splitAt(' ');
        $this->assertInternalType('array', $parts);
        $this->assertEquals(3, count($parts));
    }
    
    /**
     * Checks if splitAt() respected the provided limit.
     */
    public function testSplitAtRespectsLimit()
    {
        $parts = $this->create('hello splitted world')->splitAt(' ', 2);
        $this->assertInternalType('array', $parts);
        $this->assertEquals(2, count($parts));
    }
    
    /**
     * Ensures that the last part in the list that is returned by splitAt()
     * contains the rest of the string if a limit was provided as second
     * argument.
     */
    public function testSplitAtReturnsRestOfStringAsLastPartIfLimitIsProvided()
    {
        $parts = $this->create('hello splitted world')->splitAt(' ', 2);
        $this->assertInternalType('array', $parts);
        $last = array_pop($parts);
        $this->assertEquals('splitted world', $last);
    }
    
    /**
     * Checks if splitAt() accepts a string object as argument.
     */
    public function testSplitAtAcceptsStringObjectAsArgument()
    {
        $parts = $this->create('hello splitted world')->splitAt($this->create(' '));
        $this->assertInternalType('array', $parts);
        $this->assertEquals(3, count($parts));
    }
    
    /**
     * Ensures that compareTo() returns -1 if the string is less than the provided string.
     */
    public function testCompareToReturnsMinusOneIfStringIsLessThanComparedString()
    {
        $result = $this->create('a')->compareTo('c');
        $this->assertEquals(-1, $result);
    }
    
    /**
     * Ensures that compareTo() returns 0 if the compared strings are equal.
     */
    public function testCompareToReturnsZeroIfStringsAreEqual()
    {
        $result = $this->create('a')->compareTo('a');
        $this->assertEquals(0, $result);
    }
    
    /**
     * Ensures that compareTo() returns 1 if the string is greater than the provided string.
     */
    public function testCompareToReturnsOneIfStringIsGreaterThanComparedString()
    {
        $result = $this->create('c')->compareTo('a');
        $this->assertEquals(1, $result);
    }
    
    /**
     * Checks if compareTo() accepts a string object as argument.
     */
    public function testCompareToAcceptsStringObjectAsArgument()
    {
        $result = $this->create('c')->compareTo($this->create('c'));
        $this->assertEquals(0, $result);
    }
    
    /**
     * Ensures that compareTo() converts the charset of the provided string
     * object before performing the comparison.
     */
    public function testCompareToUnifiesCharsetBeforeComparison()
    {
        $latin1 = $this->create('äöü')->convertTo(PCharset::LATIN1);
        $this->assertStringObject($latin1);
        // The raw strings are not equal, but compareTo() should unifiy the charset
        // before performing the comparison.
        $result = $this->create('äöü')->compareTo($latin1);
        $this->assertEquals(0, $result);
    }
    
    /**
     * Checks if concat() returns the same string object.
     */
    public function testConcatReturnsSameStringObject()
    {
        $result  = $this->create('abc');
        $result2 = $result->concat('xyz');
        $this->assertStringObject($result2);
        $this->assertSame($result, $result2);
    }
    
    /**
     * Ensures that concat() returns the string itself if provided string value
     * has length 0.
     */
    public function testConcatReturnsCurrentStringIfProvidedStringHasLengthZero()
    {
        $current = $this->create('abc');
        $this->assertSame($current, $current->concat(''));
    }
    
    /**
     * Checks if concat() appends the provided string.
     */
    public function testConcatAppendsProvidedString()
    {
        $result = $this->create('abc')->concat('xyz');
        $this->assertStringObject($result);
        $this->assertEquals('abcxyz', $result->toString());
    }
    
    /**
     * Checks if concat() accepts a string object.
     */
    public function testConcatAcceptsStringObjectAsArgument()
    {
        $this->setExpectedException(null);
        $this->create('abc')->concat($this->create('xyz'));
    }
    
    /**
     * Checks if concat() appends the content of a provided string object.
     */
    public function testConcatAppendsStringObject()
    {
        $result = $this->create('abc')->concat($this->create('xyz'));
        $this->assertStringObject($result);
        $this->assertEquals('abcxyz', $result->toString());
    }
    
    /**
     * Ensures that concat() converts the charset of the provided string object
     * automatically if necessary.
     */
    public function testConcatPerformsCharsetConversionIfNecessary()
    {
        $latin1Content = iconv(PCharset::UTF8, PCharset::LATIN1, 'äüö');
        $latin1        = $this->create($latin1Content, PCharset::LATIN1);
        $utf8          = $this->create('äüö');
        $result        = $utf8->concat($latin1);
        $this->assertStringObject($result);
        // One part of the string will be broken if no conversion was performed.
        $this->assertEquals('äüöäüö', $result->toString());
    }
    
    /**
     * Ensures that the string object that is returned by concat() uses the same
     * charset as the current string.
     */
    public function testConcatReturnsStringWithSameCharsetAsCurrentString()
    {
        $latin1 = $this->create('xyz', PCharset::LATIN1);
        $utf8   = $this->create('abc');
        // The string do not share the same charset, therefor the method must decide which one to use.
        $result = $latin1->concat($utf8);
        $this->assertStringObject($result);
        $this->assertEquals(PCharset::LATIN1, $result->getCharset());
    }
    
    /**
     * Ensures that concat() throws an exception if an invalid argument is passed.
     */
    public function testConcatThrowsExceptionIfInvalidArgumentIsProvided()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->create('test')->concat(new stdClass());
    }
    
    /**
     * Ensures that offsetExists() returns false if a negative index is provided.
     */
    public function testOffsetExistsReturnsFalseIfNegativeIndexIsProvided()
    {
        $object = $this->create('abc');
        $this->assertFalse(isset($object[-1]));
    }
    
    /**
     * Ensures that offsetExists() returns false if a an index that exceeds the
     * length of the string is passed.
     */
    public function testOffsetExistsReturnsFalseIfProvidedIndexExceedsStringLength()
    {
        $object = $this->create('abc');
        $this->assertFalse(isset($object[3]));
    }
    
    /**
     * Ensures that offsetExists() returns true if a valid character index is passed.
     */
    public function testOffsetExistsReturnsTrueIfValidCharacterIndexIsProvided()
    {
        $object = $this->create('abc');
        $this->assertTrue(isset($object[2]));
    }
    
    /**
     * Ensures that offsetGet() throws an exception if an invalid index is passed.
     */
    public function testOffsetGetThrowsExceptionIfInvalidIndexIsProvided()
    {
        $this->setExpectedException('OutOfBoundsException');
        $object = $this->create('abc');
        $object[5];
    }
    
    /**
     * Checks if offsetGet() returns the correct character.
     */
    public function testOffsetGetReturnsCorrectCharacter()
    {
        $object = $this->create('abc');
        $this->assertEquals('b', $object[1]);
    }
    
    /**
     * Checks if offsetGet() treats multi-byte characters correctly.
     */
    public function testOffsetGetHandlesMultiByteCharacterCorrectly()
    {
        $object = $this->create('äüö');
        $this->assertEquals('ü', $object[1]);
    }
    
    /**
     * charAt is the same as arrayAccess
     */
    public function testCharAt()
    {
        $object = $this->create('äüö');
        $this->assertEquals('ü', $object->charAt(1));
    }
    
    /**
     * charAt is the same as arrayAccess
     */
    public function testCharAtException()
    {
        $object = $this->create('äüö');
        $this->setExpectedException('OutOfBoundsException');
        $object->charAt(5);
    }
    
    /**
     * Ensures that offsetSet() replaces the desired character
     */
    public function testOffsetSet()
    {
        $object = $this->create('abc');
        $object[2] = 'z';
        $this->assertEquals('abz', $object->toString());
    }
    
    /**
     * Ensures that offsetSet() replaces the desired character
     */
    public function testOffsetSetMultiByte()
    {
        $object = $this->create('abc');
        $object[2] = 'Ä';
        $this->assertEquals('abÄ', $object->toString());
    }
    
    
    /**
     * Ensures that offsetSet() throws an out of bounds exception
     */
    public function testOffsetSetThrowsOutOfBoundsException()
    {
        $this->setExpectedException('OutOfBoundsException');
        $object = $this->create('abc');
        $object[4] = 'z';
    }
    
    /**
     * Ensures that offsetUnset() throws an exception as this method
     * is not supposed to be called.
     */
    public function testOffsetUnsetThrowsException()
    {
        $this->setExpectedException('LogicException');
        $object = $this->create('abc');
        unset($object[0]);
    }
    
    /**
     * ensures md5 is the hascode default algo
     */
    public function testHashcodeReturnsNewPstring()
    {
        $raw = 'test';
        $pstring = new PString($raw);
        $this->assertInstanceOf('Pstring', $pstring->hashCode());
        $this->assertNotSame($pstring, $pstring->hashCode());
    }
    
    /**
     * ensures md5 is the hascode default algo
     */
    public function testHashcodeUsesMd5AsDefault()
    {
        $raw = 'test';
        $pstring = new PString($raw);
        
        $this->assertEquals(md5($raw), $pstring->hashCode()->toString());
    }
    
    public function valueOfProvider()
    {
        return array(
            array('abc', 'abc'),
            array('', ''),
            array(null, ''),
            array(0, '0'),
            array(1, '1'),
            array(-1.0, '-1.0'),
            array((float)-0.00000000345, '-0.00000000345'),
            array((double)-0.00000000345, '-0.00000000345'),
            array(new PString('abc'), 'abc'),
        );
    }
    
    /**
     * @dataProvider valueOfProvider
     * 
     * @param mixed $type
     * @param string $expected
     */
    public function testValueOf($type, $expected)
    {
        $string = Pstring::valueOf($type);
        $this->assertStringObject($string);
        $this->assertEquals($expected, $string->toString());
    }
    
    /**
     * ensures that the same object is returned by valueOf
     */
    public function testValueOfReturnsSamePString()
    {
        $string = new PString('abc');
        $this->assertSame($string, Pstring::valueOf($string));
    }
    
    /**
     * ensures that the same object is returned by valueOf
     */
    public function testValueOfReturnsSamePStringWithDefinedCharset()
    {
        $string = new PString('äüßa');
        $this->assertSame($string, Pstring::valueOf($string, new PCharset()));
    }
    
    /**
     * ensures that the same object is returned by valueOf
     */
    public function testValueOfReturnsSamePStringWithDifferentCharset()
    {
        $string = new PString('abcä', PCharset::LATIN1);
        $this->assertSame($string, Pstring::valueOf($string, new PCharset(PCharset::UTF8)));
    }
    
    /**
     * tests the format function
     */
    public function testFormat()
    {
        $string = Pstring::format("An %s", array('Äpple'));
        $this->assertStringObject($string);
        $this->assertEquals("An Äpple", $string->toString());
    }
    
    /**
     * weak test. expects a phpunit error if vsprint is called with too few args
     */
    public function testFormatWithTooFewArgs()
    {
        $this->setExpectedException('PHPUnit_Framework_Error', 'Too few arguments');
        $string = Pstring::format("An %s %s", array('Äpple'));
    }
    
    /**
     * Creates a string object.
     *
     * @param string $string
     * @param string $charset
     * @return Mol_DataType_String
     */
    protected function create($string, $charset = PCharset::UTF8)
    {
        $object = new PString($string, $charset);
        $this->assertStringObject($object);
        return $object;
    }
    
    /**
     * Asserts that the provided value is an instance of Mol_DataType_String.
     *
     * @param mixed $object
     */
    protected function assertStringObject($object)
    {
        $this->assertInstanceOf('PString', $object);
    }
    
}