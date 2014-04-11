<?php

namespace flims;

abstract class AbstractDirectory
{
    /**
     * @var \flims\AbstractDirectory
     */
    protected static $instance = null;
    
    /**
     * @return \flims\AbstractDirectory
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
    
    /**
     *
     * @var array Array<IDirectoryEntry>
     */
    protected $entries = array();
    
    /**
     * Register a directory entry
     * @param \flims\IDirectoryEntry $entry
     * @return \flims\IDirectory $this
     */
    public function register(IDirectoryEntry $entry)
    {
        $this->entries[$entry->getCode()] = $entry;
    }
    
    /**
     * Locate an entry by its code
     * @param string $entryCode
     * @return \flims\IDirectoryEntry
     */
    public function locate($entryCode)
    {
        if (!$this->isRegistered($entryCode)) {
            throw new DirectoryEntryNotFoundException(sprintf('Cound not locate entry with code: %s', $entryCode));
        }
        
        return $this->entries[$entryCode];
    }
    
    /**
     * Locate all entries, optionnaly for a single type
     * @param integer $type
     * @return \flims\IDirectoryEntry[]
     */
    public function locateAll($type = null)
    {
        if (!$type) {
            return array_values($this->entries);
        }
        
        $entries = array();
        foreach ($this->entries as $entry) {
            if ($entry->hasType($type)) {
                $entries[] = $entry;
            }
        }
        
        return $entries;
    }
    
    /**
     * Is this entry code registered?
     * @param string $entryCode
     * @return boolean
     */
    public function isRegistered($entryCode)
    {
        return isset($this->entries[$entryCode]);
    }
}
