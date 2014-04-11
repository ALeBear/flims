<?php

namespace flims\apiProvider\freebase;

use flims\IDirectoryEntry;
use flims\MovieId;
use flims\apiProvider\TApiProvider;
use flims\apiProvider\IProvider;
use flims\apiProvider\IListingProvider;
use flims\apiProvider\freebase\guzzle\Client as GuzzleFreebaseClient;

class ApiProvider implements IProvider, IListingProvider, IDirectoryEntry
{
    use TApiProvider;
    
    const CODE = 'f';
        
    /**
     * @var flims\apiProvider\freebase\guzzle\Client
     */
    protected $guzzleClient = null;
    
    /**
     * Get the movies corresponding to a definition
     * @param string $definition
     * @param integer $offsetStart
     * @param integer $offsetLength
     * @return flims\MovieId[]
     */
    public function getMovieIds($definition, $offsetStart, $offsetLength)
    {
        if (!($decodedDef = @json_decode($definition, true))) {
            throw new InvalidDefinitionException(sprintf('Invalid Freebase listing definition: %s', $definition));
        }
        
        if (!isset($decodedDef['query'])) {
            throw new InvalidDefinitionException(sprintf('Invalid Freebase listing definition, does not contain "query" key: %s', $definition));
        }
        
        //Simple search webservice
        $client = $this->getGuzzleClient();
        $command = $client->getCommand('MQLRead', array('query' => json_encode($decodedDef['query'])));
        $result = array();
        foreach ($client->execute($command)->getAll()['result'][0]['/type/reflect/any_reverse'] as $item) {
            $result[] = MovieId::fromProvider(self::CODE, $item['guid']);
        }

        return $result;
    }
            
    /**
     * @return string
     */
    public function getCode()
    {
        return self::CODE;
    }
    
    /**
     * @return \Guzzle\Service\Client
     */
    protected function getGuzzleClient()
    {
        if (is_null($this->guzzleClient)) {
            $this->guzzleClient = GuzzleFreebaseClient::factory(array(
                GuzzleFreebaseClient::CURL_OPTIONS => $this->getCurlOptions(),
                'apikey' => self::$conf->get('freebase/apikey')));
        }
        
        return $this->guzzleClient;
    }
}