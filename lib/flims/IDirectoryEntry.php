<?php

namespace flims;

/**
 * Interface for entities that will be locatable in directories
 */
interface IDirectoryEntry
{
    /**
     * @return string
     */
    public function getCode();
    
    /**
     * Is this entry of the given type?
     * @param integer $type
     * @return boolean
     */
    public function hasType($type);
    
    /**
     * An entry can have one or more types (usually bit-coded values)
     * @return integer
     */
    public function getTypes();
}
