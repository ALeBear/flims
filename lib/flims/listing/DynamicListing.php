<?php

namespace flims\listing;

use flims\apiProvider\IListingProvider;
use flims\apiProvider\Directory;

/**
 * Dynamic listing tied to a query on a provider
 * @Entity
 */
class DynamicListing extends AbstractListing
{
    /**
     * The DB definition, contains all the provider code and the provider's
     * definition
     * @Column(type="string", length=3000)
     * @var string
     */
    protected $definition;
    
    /**
     * @var \flims\apiProvider\IListingProvider 
     */
    protected $provider;
    
    /**
     * @var string
     */
    protected $providerDefinition;
    
    /**
     * Factory method, from a provider and its definition
     * @param \flims\apiProvider\IListingProvider $provider
     * @param string $definition
     * @return \flims\listing\DynamicListing
     */
    public static function fromDefinition($definition)
    {
        $decoded = static::decodeDefinition($definition);
        return static::fromProviderCode($decoded['provider'], json_encode($decoded['definition']));
    }
    
    /**
     * Factory method, from a provider and its definition
     * @param \flims\apiProvider\IListingProvider $provider
     * @param string $providerDefinition
     * @return \flims\listing\DynamicListing
     */
    public static function fromProvider(IListingProvider $provider, $providerDefinition)
    {
        return new static($provider, $providerDefinition);
    }
    
    /**
     * Factory method, from a provider code and its definition
     * @param \flims\apiProvider\IListingProvider $provider
     * @param string $providerDefinition
     * @return \flims\listing\DynamicListing
     */
    public static function fromProviderCode($providerCode, $providerDefinition)
    {
        return static::fromProvider(Directory::getInstance()->locate($providerCode), $providerDefinition);
    }
    
    /**
     * @param \flims\apiProvider\IListingProvider $provider
     * @param string $definition
     */
    protected function __construct(IListingProvider $provider, $definition)
    {
        $this->provider = $provider;
        $this->providerDefinition = $definition;
        $decoded = @json_decode($definition, true);
        if (!$decoded) {
            throw new InvalidDefinitionException(sprintf('Cannot json-decode the provider definition for "%s"', $this->getName()));
        }
        $this->definition = json_encode(array('provider' => $provider->getCode(), 'description' => $decoded));
    }
    
    /**
     * Decode a json definition
     * @param string $definition
     * @return array The json decoded array (certified)
     */
    protected static function decodeDefinition($definition)
    {
        $decoded = @json_decode($definition, true);
        if (!$decoded) {
            throw new InvalidDefinitionException(sprintf('Cannot json-decode the definition for "%s"', $this->getName()));
        }
        if (!isset($decoded['provider']) || !isset($decoded['definition'])) {
            throw new InvalidDefinitionException(sprintf('Malformed definition for "%s"', $this->getName()));
        }
        
        return $decoded;
    }
    
    /**
     * @param integer $start
     * @param integer $offset
     * @return \flims\MovieId[]
     */
    public function getMovieIds($offsetStart = 0, $offsetLength = self::DEFAULT_LENGTH)
    {
        if (!$this->provider) {
            $decoded = $this->decodeDefinition($this->definition);
            $this->provider = Directory::getInstance()->locate($decoded['provider']);
            $this->providerDefinition = json_encode($decoded['definition']);
        }
        
        return $this->provider->getMovieIds($this->providerDefinition, $offsetStart, $offsetLength);
    }
}