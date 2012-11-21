<?php
/**
 * PComparable
 * 
 * Mimics the Comparable interface. 
 * 
 * "This interface imposes a total ordering on the objects of each class that implements it."
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
interface PComparable
{
    /**
     * Compares the object to the "other" passed value.
     * 
     * @param mixed $other
     * 
     * @return int (-1|0|1)
     */
    public function compareTo($other);
}
