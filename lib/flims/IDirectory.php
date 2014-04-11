<?php

namespace flims;

/**
 * Interface for all directories
 */
interface IDirectory
{
    /**
     * @return \flims\IDirectory The singleton
     */
    public static function getInstance();
    
    /**
     * Register a directory entry
     * @param \flims\IDirectoryEntry $entry
     * @return \flims\IDirectory $this
     */
    public function register(IDirectoryEntry $entry);
    
    /**
     * Locate an entry by its code
     * @param string $entryCode
     * @return \flims\IDirectoryEntry
     */
    public function locate($entryCode);
    
    /**
     * Locate all entries, optionnaly for a single type
     * @param integer $type
     * @return \flims\IDirectoryEntry[]
     */
    public function locateAll($type = null);
    
    /**
     * Is this entry code registered?
     * @param string $entryCode
     * @return boolean
     */
    public function isRegistered($entryCode);
}
