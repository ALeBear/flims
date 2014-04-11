<?php

namespace flims\movie\detail;

use flims\movie\AbstractDetail;

class Image extends AbstractDetail
{
    /**
     * @var string
     */
    protected $url;
    
    /**
     * @var string
     */
    protected $providerCode;
    
    /**
     * @param string $value
     * @return \flims\movie\StringDetail $this
     */
    public function setUrl($value, $providerCode = null)
    {
        $this->url = $value;
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
     * @return string The provider code of the source
     */
    public function getSource()
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
        return $this->url;
    }
}