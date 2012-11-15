<?php
/**
 * Class representing a charset
 *
 * @author daniel
 */
class PCharset
{
    /**
     * Constant for the name of the UTF-8 charset.
     *
     * @var string
     */
    const UTF8 = 'UTF-8';
    
    /**
     * Constant for the name of the ISO-8859-1 charset.
     *
     * @var string
     */
    const LATIN1 = 'ISO-8859-1';
    
    /**
     * Cached list of supported charsets.
     *
     * @var array(string)|null
     */
    protected static $charsets = null;
    
    /**
     * Cached mapping of accepted charset names to charsets.
     *
     * The names are used as key, the charsets as value.
     *
     * @var array(string=>string)|null
     */
    protected static $namesToCharsets = null;
    
    /**
     *
     * @var string
     */
    protected $charset;
    
    /**
     * pass a valid charset
     * 
     * @param string $charset
     */
    public function __construct($charset = self::UTF8)
    {
        $this->assertCharset($charset);
        $this->charset = $this->unifyCharset($charset);
    }
    
    /**
     * Asserts that $charset is an available charset.
     *
     * @param string $charset
     * @throws InvalidArgumentException If an invalid charset is provided.
     */
    protected function assertCharset($charset)
    {
        $namesToCharsets = self::getCharsetNameMapping();
        if (isset($namesToCharsets[$charset])) {
            // Charset is valid.
            return;
        }
        $format  = '"%s" is not a valid charset. The following charsets are supported: %s';
        $message = sprintf($format, $charset, implode(', ', self::getCharsets()));
        throw new InvalidArgumentException($message);
    }
    
    /**
     * Returns a mapping of accepted charset names to charsets.
     *
     * For example "UTF-8" and "utf8" are both valid charset names.
     *
     * @return array(string=>string)
     */
    protected static function getCharsetNameMapping()
    {
        if (self::$namesToCharsets === null) {
            self::$namesToCharsets = array();
            foreach (self::getCharsets() as $charset) {
                self::$namesToCharsets[$charset] = $charset;
                foreach (mb_encoding_aliases($charset) as $alias) {
                    self::$namesToCharsets[$alias] = $charset;
                }
            }
        }
        return self::$namesToCharsets;
    }
    
    /**
     * Returns all available charsets.
     *
     * @return array(string)
     */
    protected static function getCharsets()
    {
        if (self::$charsets === null) {
            self::$charsets = mb_list_encodings();
        }
        return self::$charsets;
    }
    
    /**
     * Maps charset aliases to charset names.
     *
     * The provided charset name must be valid.
     *
     * @param string $charset
     * @return string
     */
    protected function unifyCharset($charset)
    {
        $namesToCharsets = self::getCharsetNameMapping();
        return $namesToCharsets[$charset];
    }
    
    /**
     * checks if a charset is the same type
     * 
     * @param string|PCharset $charset
     * 
     * @return boolean
     */
    public function equals($charset)
    {
        $charset = (string)$charset;
        $this->assertCharset($charset);
        return $this->charset == $this->unifyCharset($charset);
    }
    
    /**
     * toString method
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->charset;
    }
    
    /**
     * Asserts that the string uses the given charset.
     *
     * Please note:
     * It is only checked if the provided string is a valid byte sequence
     * in the provided encoding.
     *
     * @param string $string
     * @param string $charset
     * @throws InvalidArgumentException If the string cannot be represented in the provided charset.
     */
    public function assertStringHasSameCharset($string)
    {
        if (mb_detect_encoding($string, $this->charset, true) === false) {
            $message = 'String is not encoded as "' . $this->charset . '".';
            throw new InvalidArgumentException($message);
        }
    }
}

