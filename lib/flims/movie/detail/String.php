<?php

namespace flims\movie\detail;

use flims\movie\AbstractDetail;

class String extends AbstractDetail
{
    /**
     * @var string
     */
    protected $value;
    
    /**
     * @var string
     */
    protected $providerCode;
    
    /**
     * @param string $value
     * @param string @providerCode
     * @return \flims\movie\StringDetail $this
     */
    public function setValue($value, $providerCode = null)
    {
        $this->value = $value;
        $this->providerCode = $providerCode;
        return $this;
    }
    
    /**
     * @return string The provider code of the source
     */
    public function getProviderCode()
    {
        return $this->providerCode;
    }
    
    /**
     * Is this detail filled by a movie provider?
     * @return boolean
     */
    public function isFilled()
    {
        return (boolean) $this->providerCode;
    }
    
    /**
     * @return string The string representation, sometimes it's what we show the
     * user
     */
    public function __toString()
    {
        return $this->value;
    }
}