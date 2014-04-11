<?php

namespace flims;

/**
 * Wrapper around services for the application
 */
class ServiceEntry implements IDirectoryEntry
{
    /**
     * @var string
     */
    protected $code;
    
    /**
     * @var mixed
     */
    protected $concreteService;

    /**
     * @param string $code
     * @param mixed $concreteService
     */
    public function __construct($code, $concreteService)
    {
        $this->code = $code;
        $this->concreteService = $concreteService;
    }
    
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * Is this entry of the given type?
     * @param integer $type
     * @return boolean
     */
    public function hasType($type)
    {
        return true;
    }
    
    /**
     * @return integer
     */
    public function getTypes()
    {
        return null;
    }
    
    /**
     * @return mixed
     */
    public function getConcreteService()
    {
        return $this->concreteService;
    }
}
