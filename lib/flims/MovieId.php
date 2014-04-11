<?php

namespace flims;

/**
 * Class that encapsulates a movie ID, which mostly aggregates a provider code
 * and an id valid for it.
 */
class MovieId
{
    const UUIDS_GLUE = '|';
    
    /**
     * @var string[]
     */
    protected $uuids = array();
    
    /**
     * It's merely the first id inserted
     * @var string
     */
    protected $defaultUuid;
    
    
    /**
     * @param string $value
     * @return \flims\MovieId
     */
    public static function fromUuids($value)
    {
        $id = new static();
        foreach (explode(static::UUIDS_GLUE, $value) as $uuid) {
            $id->addFromUuid($uuid);
        }
        return $id;
    }
    
    /**
     * @param string $value
     * @return \flims\MovieId
     */
    public static function fromUuid($value)
    {
        return static::fromUuids($value);
    }
    
    /**
     * @param string $providerCode
     * @param string $id
     * @return \flims\MovieId
     */
    public static function fromProvider($providerCode, $id)
    {
        return static::fromUuid($providerCode . $id);
    }
          
    /**
     * @return string
     */
    public function getUUID($providerCode = null)
    {
        return $providerCode ? $providerCode . $this->getId($providerCode) : $this->defaultUuid;
    }
    
    /**
     * @param string $providerCode
     * @return string
     */
    public function getId($providerCode = null)
    {
        if (($providerCode && !isset($this->uuids[$providerCode])) || !$this->defaultUuid) {
            throw new ProviderMovieIdMissingException(sprintf('Movie does not have an id for provider "%s" (ids: %s)', $providerCode, (string) $this));
        }
        return substr($providerCode ? $this->uuids[$providerCode] : $this->defaultUuid, 1);
    }
    
    /**
     * @return string
     */
    public function getDefaultProviderCode()
    {
        return $this->defaultUuid ? $this->defaultUuid{0} : '';
    }
    
    /**
     * @param string $value
     * @return \flims\MovieId
     */
    public function addFromUuid($value)
    {
        if (strlen($value) > 1) {
            $this->uuids[$value{0}] = $value;
            $this->defaultUuid || $this->defaultUuid = $value;
        }
        return $this;
    }
    
    /**
     * @param string $providerCode
     * @param string $id
     * @return \flims\MovieId $this
     */
    public function addFromProvider($providerCode, $id)
    {
        return $this->addFromUuid($providerCode . $id);
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return implode(static::UUIDS_GLUE, $this->uuids);
    }
}