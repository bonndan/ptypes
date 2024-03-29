<?php
/**
 * PString
 *
 * @category PHP
 * @author Matthias Molitor <matthias@matthimatiker.de>
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * 
 * @copyright 2012 Matthias Molitor
 * @license http://www.opensource.org/licenses/BSD-3-Clause BSD License
 * @link https://github.com/Matthimatiker/MolComponents
 * @since 14.06.2012
 */

/**
 * Class that simplifies charset-dependent string handling.
 *
 * Hint:
 * Have a closer look at Mol_Util_String if you are searching for
 * string methods that do not require knowledge about the charset.
 *
 * == Description ==
 *
 * Each string is represented by an object that encapsulates the
 * raw string value and the charset.
 *
 * The content of a string object is not changable, if a modification
 * is performed then a new string object will be created and returned.
 *
 * If necessary methods take the charset into account. Therefore it
 * is possible to compare string with different charsets and so on.
 *
 * == Usage ==
 *
 * String objects are instantiated via create():
 * <code>
 * $stringObject = PString::create('my string');
 * </code>
 *
 * If no charset is provided then UTF-8 is assumed, but you may
 * specify the charset of the provided string too:
 * <code>
 * $stringObject = new PString('my string', 'latin1');
 * </code>
 *
 * Once a string object is created you may use its methods to
 * inspect the string:
 * <code>
 * $stringObject->endsWith('Test');
 * </code>
 *
 * All methods respect the charset if necessary, so multi-byte characters
 * are handled correctly:
 * <code>
 * $stringObject = PString::create('äüö', 'UTF-8');
 * // Returns: array('ä', 'ü', 'ö')
 * $characters   = $stringObject->toCharacters();
 * </code>
 *
 * @category PHP
 * @package Mol_DataType
 * @author Matthias Molitor <matthias@matthimatiker.de>
 * @copyright 2012 Matthias Molitor
 * @license http://www.opensource.org/licenses/BSD-3-Clause BSD License
 * @link https://github.com/Matthimatiker/MolComponents
 * @since 14.06.2012
 */
class PString extends PType implements IteratorAggregate, ArrayAccess, Countable, PComparable
{
    /**
     * The raw string value.
     *
     * @var string
     */
    protected $value = null;
    
    /**
     * The charset of the string.
     *
     * @var PCharset
     */
    protected $charset = null;
    
    /**
     * Cached list of characters in this string.
     *
     * Contains null if the characters were not requested yet.
     *
     * @var array(string)|null
     */
    private $characters = null;
    
    /**
     * Creates a string object from the given raw string.
     *
     * It is assumed that the string uses the mentioned charset.
     *
     * @param string|PString $string The raw string.
     * @param string $charset The charset of the string.
     * @return PString
     */
    public static function create($string, $charset = PCharset::UTF8)
    {
        if ($string instanceof self) {
            return $string->convertTo($charset);
        }
        return new self($string, $charset);
    }
    
    /**
     * Creates a string object
     * 
     * If not charset is passed the default charset is used (utf8).
     *
     * @param string   $string The raw string.
     * @param string|PCharset $charset The charset of the string.
     */
    public function __construct($string, $charset = null)
    {
        if (is_string($charset)) {
            $charset = new PCharset($charset);
        }
        
        if ($charset == null) {
            $charset = new PCharset();
        }
        
        $charset->assertStringHasSameCharset($string);
        $this->value   = $string;
        $this->charset = $charset;
    }
    
    /**
     * Returns the current charset of the string.
     *
     * @return PCharset
     */
    public function getCharset()
    {
        return $this->charset;
    }
    
    /**
     * Converts the string into the requested charset.
     *
     * @param string $charset
     * @return PString The string in the requested charset.
     */
    public function convertTo($charset)
    {
        if (is_string($charset)) {
            $charset = new PCharset($charset);
        }
        
        if ($this->charset->equals($charset)) {
            // No conversion required.
            return $this;
        }
        
        $this->value = iconv($this->charset, (string)$charset . '//TRANSLIT', $this->value);
        $this->charset = $charset;
        return $this;
    }
    
    /**
     * Returns a hash code calculated using the hash() function
     * 
     * The implementation differs from the Java one. md5 is used as default and
     * a string is returned
     * 
     * @param string $algo the used algoritm method for hash()
     * @return Pstring
     * @see http://stackoverflow.com/questions/8804875/php-internal-hashcode-function
     */
    public function hashCode($algo = 'md5')
    {
        return new PString(hash($algo, $this->toString()));
    }
    
    /**
     * Returns the index of the first occurrence of $needle.
     *
     * If $needle was not found then -1 will be returned.
     * If provided as second argument then the search will
     * begin at the given index.
     *
     * @param string|PString $needle
     * @param integer $fromIndex
     * @return integer Index or -1 if $needle was not found.
     */
    public function indexOf($needle, $fromIndex = 0)
    {
        $position = mb_strpos($this->value, $this->toValue($needle), $fromIndex, $this->charset);
        if ($position === false) {
            return -1;
        }
        return $position;
    }
    
    /**
     * Returns the index of the last occurrence of $needle.
     *
     * If $needle was not found then -1 will be returned.
     * The search is performed from right to left.
     * If $fromIndex is provided then the search will begin at that index.
     *
     * @param string|PString $needle
     * @param integer|null $fromIndex
     * @return integer Index or -1 if $needle was not found.
     */
    public function lastIndexOf($needle, $fromIndex = null)
    {
        $search = $this->value;
        if ($fromIndex !== null) {
            // Per default mb_strrpos() searches from left to right and starts at the given offset.
            // We cut of the end of the string starting at ($fromIndex + 1) to simulate searching backwards.
            $search = $this->rawSubString(0, $fromIndex + 1);
        }
        $position = mb_strrpos($search, $this->toValue($needle), null, $this->charset);
        if ($position === false) {
            return -1;
        }
        return $position;
    }
    
    /**
     * Returns the indexes of all occurrences of $needle.
     *
     * @param string|PString $needle
     * @return array(integer)
     */
    public function indexesOf($needle)
    {
        $needle  = $this->toValue($needle);
        $offset  = 0;
        $indexes = array();
        while (($position = $this->indexOf($needle, $offset)) !== -1) {
            $indexes[] = $position;
            // Search after current match in next iteration.
            $offset = $position + 1;
        }
        return $indexes;
    }
    
    /**
     * Checks if the string starts with the provided prefix.
     *
     * @param string|PString $prefix
     * @return boolean True if the string starts with the prefix, false otherwise.
     */
    public function startsWith($prefix)
    {
        return mb_strpos($this->value, $this->toValue($prefix)) === 0;
    }
    
    /**
     * Checks if the string ends with the provided suffix.
     *
     * @param string|PString $suffix
     * @return boolean True if the string ends with the suffix, false otherwise.
     * @todo mb_strlen?
     */
    public function endsWith($suffix)
    {
        $suffix = $this->toValue($suffix);
        $expectedPosition = mb_strlen($this->value) - mb_strlen($suffix);
        return mb_strrpos($this->value, $suffix) === $expectedPosition;
    }
    
    /**
     * Checks if the string contains the provided needle.
     *
     * @param string|PString $needle
     * @return boolean True if the string contains the needle, false otherwise.
     */
    public function contains($needle)
    {
        return mb_strpos($this->value, $this->toValue($needle)) !== false;
    }
    
    /**
     * Checks if the string contains any of the provided needles.
     *
     * @param array(string|PString) $needles
     * @return boolean True if the string contains a needle, false otherwise.
     */
    public function containsAny(array $needles)
    {
        $needles = $this->toValues($needles);
        $subject = $this->value;
        
        if (count($needles) === 0) {
            return true;
        }
        foreach ($needles as $needle) {
            /* @var $needle string */
            if (mb_strpos($subject, (string)$needle) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Checks if the string contains all of the provided needles.
     *
     * @param array(string|PString) $needles
     * @return boolean True if the string contains all needles, false otherwise.
     */
    public function containsAll(array $needles)
    {
        $needles = $this->toValues($needles);
        
        foreach ($needles as $needle) {
            /* @var $needle string */
            if (mb_strpos($this->value, (string)$needle) === false) {
                return false;
            }
        }
        return true;
        
    }
    
    /**
     * Removes the given prefix from the string.
     *
     * This method has no effect if the string does not start with $prefix.
     *
     * @param string|PString $prefix
     * @return PString String without prefix.
     */
    public function removePrefix($prefix)
    {
        $prefix = $this->toValue($prefix);
        if ($this->startsWith($prefix)) {
            $this->value = substr($this->value, mb_strlen($prefix));
        }
        
        return $this;
    }
    
    /**
     * Removes the given suffix from the string.
     *
     * This method has no effect if the string does not end with $suffix.
     *
     * @param string|PString $suffix
     * @return PString String without suffix.
     */
    public function removeSuffix($suffix)
    {
        $suffix = $this->toValue($suffix);
        if ($this->endsWith($suffix)) {
            $this->value = mb_substr($this->value, 0, mb_strlen($this->value) - mb_strlen($suffix));
        }
        
        return $this;
    }
    
    /**
     * Replaces all occurrences of $searchOrMapping by $replace.
     *
     * This method provides 3 signatures:
     *
     * replace(string, string):
     * <code>
     * $result = $myString->replace('search', 'replace');
     * </code>
     * Replaces all occurrences of "search" by "replace".
     *
     * replace(array(string), string):
     * <code>
     * $needles = array(
     *     'first',
     *     'seconds'
     * );
     * $result = $myString->replace($needles, 'replace');
     * </code>
     * Replaces all string that are contained in the $needles array by "replace".
     *
     * replace(array(string=>string)):
     * <code>
     * $mapping = array(
     *     'first' => 'last',
     *     'hello' => 'world'
     * );
     * $result = $myString->replace($mapping);
     * </code>
     * Expects an associative array that represents a mapping of strings
     * as argument.
     * The keys are replaced by the assigned values.
     * In this example occurences of "first" are replaced by "last" and
     * "hello" is replaced by "world".
     *
     * @param string|PString|array(integer|string=>string|PString) $searchOrMapping
     * @param string|PString $replace
     * @return PString The string with applied replacements.
     */
    public function replace($searchOrMapping, $replace = null)
    {
        $search = $this->toValues($searchOrMapping);
        if ($replace === null && is_array($searchOrMapping)) {
            // Mapping provided.
            $replace = $search;
            $search  = array_keys($searchOrMapping);
        }
        
        $this->value = self::_replace($this->value, $search, $replace);
        return $this;
    }
    
    protected static function _replace($subject, $searchOrMapping, $replace = null)
    {
        $search = $searchOrMapping;
        if ($replace === null && is_array($searchOrMapping)) {
            // Mapping provided.
            $search  = array_keys($searchOrMapping);
            $replace = array_values($searchOrMapping);
        }
        
        return str_replace($search, $replace, $subject);
    }
    
    /**
     * Returns a new string which contains the requested substring.
     *
     * Starts at $startIndex and extracts $length characters.
     * If $length is not provided then the substring will
     * extend to the end of the string.
     *
     * @param integer $startIndex The start index.
     * @param integer|null $length The length in characters.
     * @return PString The substring.
     */
    public function subString($startIndex, $length = null)
    {
        $subString = $this->rawSubString($startIndex, $length);
        return new self($subString, $this->charset);
    }
    
    /**
     * Extracts the requested substring.
     *
     * Returns the result as simple string, not as object.
     *
     * @param integer $startIndex The start index.
     * @param integer|null $length The length in characters.
     * @return string The substring.
     */
    protected function rawSubString($startIndex, $length = null)
    {
        if ($length === null) {
            // Use a length that cannot be reached by the substring to
            // ensure that it is extended to the end of the original
            // string.
            $length = $this->lengthInBytes();
        }
        return mb_substr($this->value, $startIndex, $length, $this->charset);
    }
    
    /**
     * Converts all characters in the string to upper case.
     *
     * @return PString The string with upper case characters.
     */
    public function toUpperCase()
    {
        $this->value = mb_strtoupper($this->value, $this->charset);
        return $this;
    }
    
    /**
     * Converts all characters in the string to lower case.
     *
     * @return PString The string with lower case characters.
     */
    public function toLowerCase()
    {
        $this->value = mb_strtolower($this->value, $this->charset);
        return $this;
    }
    
    /**
     * Removes the provided characters from start and end of the string.
     *
     * @param string $characters
     * @return PString The string without leading and trailing characters.
     */
    public function trim($characters = null)
    {
        $this->value = $this->applyTrim('trim', $characters);
        return $this;
    }
    
    /**
     * Removes the provided characters from the start of the string.
     *
     * @param string $characters
     * @return PString The string without leading characters.
     */
    public function trimLeft($characters = null)
    {
        $this->value = $this->applyTrim('ltrim', $characters);
        return $this;
    }
    
    /**
     * Removes the provided characters from the end of the string.
     *
     * @param string $characters
     * @return PString The string without trailing characters.
     */
    public function trimRight($characters = null)
    {
        $this->value = $this->applyTrim('rtrim', $characters);
        return $this;
    }
    
    /**
     * Returns the reversed string.
     *
     * @return PString
     */
    public function reverse()
    {
        $characters  = array_reverse($this->toCharArray());
        $this->value = implode('', $characters);
        return $this;
    }
    
    /**
     * Adds the provided string to the end of this string.
     *
     * @param string|PString $string
     * @return PString The string with concatenated.
     */
    public function concat($string)
    {
        $string = $this->toValue($string);
        if ($this->getLengthInBytes($string) === 0) {
            return $this;
        }
        
        $this->value .= $string;
        return $this;
    }
    
    /**
     * Splits the string by using the provided delimiter.
     *
     * @param string|PString $delimiter
     * @param integer|null $limit Maximal number of parts.
     * @return array(string)
     */
    public function splitAt($delimiter, $limit = null)
    {
        if ($limit === null) {
            // Use a limit that cannot be reached by splitting the string.
            $limit = $this->lengthInBytes();
        }
        return explode($this->toValue($delimiter), $this->value, $limit);
    }
    
    /**
     * Returns the character at the specified index.
     * 
     * Uses ArrayAccess internally.
     * 
     * @param int $index
     * @return string
     * @throws OutOfBoundsException
     */
    public function charAt($index)
    {
        return $this[$index];
    }
    
    /**
     * Converts the string into an array of characters.
     *
     * @return array(string) The characters in order of occurrence in the string.
     */
    public function toCharArray()
    {
        if ($this->characters === null) {
            $this->characters = array();
            $length = $this->length();
            for ($i = 0; $i < $length; $i++) {
                $this->characters[] = $this->rawSubString($i, 1);
            }
        }
        return $this->characters;
    }
    
    /**
     * Allows iterating through the characters of the string.
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toCharArray());
    }
    
    /**
     * Checks if the strings are equal.
     *
     * @param string|PString $string
     * @return boolean True if the strings are equal, false otherwise.
     */
    public function equals($string)
    {
        return $this->value === $this->toValue($string);
    }
    
    /**
     * Returns the length of the string in characters.
     *
     * @return int
     */
    public function length()
    {
        return mb_strlen($this->value, $this->charset);
    }
    
    /**
     * Returns the length of the string in bytes.
     *
     * Some charsets require more than 1 byte to store a character,
     * therefore length() and lengthInBytes() do not have to be equal.
     *
     * @return integer
     */
    public function lengthInBytes()
    {
        return $this->getLengthInBytes($this->value);
    }
    
    /**
     * Alias of length().
     *
     * Allows to obtain the string length by using count():
     * <code>
     * $length = count($myStringObject);
     * </code>
     *
     * @return integer
     */
    public function count()
    {
        return $this->length();
    }
    
    /**
     * Checks if the string is empty.
     *
     * A string is empty if its length is 0.
     *
     * @return boolean True if the string is empty, false otherwise.
     */
    public function isEmpty()
    {
        return mb_strlen($this->value, $this->charset) == 0;
    }
    
    /**
     * Compares this string with $other.
     *
     * Returns:
     * # -1 if string is less than $other
     * #  0 if string equals $other
     * #  1 if string is greater than $other
     *
     * The COMPARE_* constants may be used to check the result.
     *
     * @param string|PString $other
     * @return integer -1 if string < $other, 0 if string == $other, 1 if string > $other.
     */
    public function compareTo($other)
    {
        $result = strcmp($this->value, $this->toValue($other));
        if ($result < 0) {
            return self::COMPARE_LESS_THAN_OTHER;
        }
        if ($result > 0) {
            return self::COMPARE_GREATER_THAN_OTHER;
        }
        return self::COMPARE_EQUALS_OTHER;
    }
    
    /**
     * Returns the PString representation of the argument.
     * 
     * Uses string casting of PHP if the value is not a PString
     * 
     * @param mixed $arg
     * @param string|PCharset $charset the desired charset
     * @return PString
     */
    public static function valueOf($arg, $charset = null)
    {
        if ($arg instanceof self) {
            if ($arg->getCharset()->equals($charset)) {
                return $arg;
            } else {
                return $arg->convertTo($charset);
            }
        }
        
        return new self((string)$arg, $charset);
    }
    
    /**
     * Returns a formatted PString using the specified format string and arguments.
     * 
     * Uses vsprintf internally. The charset is optional.
     * 
     * @param string|Pstring $string
     * @param array          $args
     * @param PCharset       $args
     * @return PString
     */
    public static function format($string, array $args, $charset = null)
    {
        $format = self::create($string, $charset);
        $value = vsprintf($format, $args);
        return new self($value, $charset);
    }
    
    /**
     * Returns the raw string (no string object).
     *
     * @return string
     */
    public function toString()
    {
        return $this->value;
    }
    
    /**
     * Alias of toString().
     *
     * Allows for outputting string objects directly:
     * <code>
     * echo $myStringObject;
     * </code>
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
    
    /**
     * Checks if the provided character index exists.
     *
     * @param integer $index
     * @return boolean True if the index is valid, false otherwise.
     */
    public function offsetExists($index)
    {
        return $index >= 0 && $index < $this->length();
    }
    
    /**
     * Returns the character at position $index.
     *
     * @param integer $index
     * @return string The character.
     * @throws OutOfBoundsException If an invalid index was provided.
     */
    public function offsetGet($index)
    {
        $this->assertIndexExists($index);
        
        $characters = $this->toCharArray();
        return $characters[$index];
    }
    
    /**
     * Overwrites the character a at given position with the second argument.
     *
     * @param integer $index
     * @param mixed $value
     * @throws OutOfBoundsException If an invalid index was provided.
     */
    public function offsetSet($index, $value)
    {
        $this->assertIndexExists($index);
        
        $characters = $this->toCharArray();
        $characters[$index] = self::valueOf($value)->toString();
        $this->value = implode('', $characters);
        
        return $this;
    }
    
    /**
     * asserts that an index exists
     * 
     * @param int $index
     * @throws OutOfBoundsException
     */
    protected function assertIndexExists($index)
    {
        if (!isset($this[$index])) {
            $template = '"%s" is not a valid index. Valid indexes span from 0 to %s.';
            $message  = sprintf($template, $index, ($this->length() - 1));
            throw new OutOfBoundsException($message);
        }
    }
    /**
     * Deleting characters is not supported.
     *
     * This method is implemented, because it is required by ArrayAccess.
     * It will always throw an exception.
     *
     * @param integer $index
     * @throws LogicException Always, as deleting characters is not supported.
     */
    public function offsetUnset($index)
    {
        throw new LogicException('Deleting characters is not supported.');
    }
    
    /**
     * Converts the provided data to a string value.
     *
     * If a string object is provided then the charset will
     * be automatically converted if necessary.
     *
     * @param string|PString|mixed $data
     * @return string The simple string value.
     * @throws InvalidArgumentException If the method cannot convert the data into a string.
     */
    protected function toValue($data)
    {
        if (is_string($data)) {
            return $data;
        }
        if ($data instanceof self) {
            return $data->convertTo($this->charset)->toString();
        }
        $received = gettype($data);
        $message  = 'Expected string or instance of ' . __CLASS__ . ', but ' . $received . ' provided.';
        throw new InvalidArgumentException($message);
    }
    
    /**
     * Converts the provided list into an array of simple string values.
     *
     * @param array(string|PString|mixed)|string|PString|mixed $data
     * @return array(string)
     */
    protected function toValues($data)
    {
        // Unify to array.
        if (!is_array($data)) {
            $data = array($data);
        }
        // Converts item to string values.
        return array_map(array($this, 'toValue'), $data);
    }
    
    /**
     * Returns the length in bytes of the provided string.
     *
     * @param string $string
     * @return integer The length in bytes.
     */
    protected function getLengthInBytes($string)
    {
        return mb_strlen($string);
    }
    
    /**
     * Applies a trim function (trim(), rtrim() or ltrim()) to the raw
     * string value and returns the result.
     *
     * Example:
     * <code>
     * $trimFunction = $this->applyTrim('rtrim', 'a');
     * </code>
     *
     * If $characters is null then whitespace will be trimmed.
     *
     * @param string $trimFunction The name of the trim function.
     * @param string|null $characters The characters that will be trimmed.
     */
    protected function applyTrim($trimFunction, $characters)
    {
        $arguments = array($this->value);
        if ($characters !== null) {
            $arguments[] = $characters;
        }
        return call_user_func_array($trimFunction, $arguments);
    }
    
}