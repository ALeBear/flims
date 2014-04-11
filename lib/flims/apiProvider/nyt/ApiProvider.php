<?php

namespace flims\apiProvider\nyt;

use flims\IDirectoryEntry;
use flims\movie\IMovie;
use flims\movie\IDetail;
use flims\MovieId;
use flims\apiProvider\TApiProvider;
use flims\apiProvider\IProvider;
use flims\apiProvider\IListingProvider;
use flims\apiProvider\IMovieProvider;
use flims\apiProvider\nyt\guzzle\Client as GuzzleNytClient;

class ApiProvider implements IProvider, IListingProvider, IMovieProvider, IDirectoryEntry
{
    use TApiProvider;
    
    const CODE = 'y';
        
    /**
     * @var flims\apiProvider\nyt\guzzle\Client
     */
    protected $guzzleClient = null;
    
    /**
     * Do this provider have stuff for this language?
     * @param string $langCode (ISO 639 or IETF)
     * @return boolean
     */
    public function providesForLanguage($langCode)
    {
        return substr($langCode, 0, 2) == 'en';
    }
    
    /**
     * Do this provider have this detail?
     * @param string $detailCode
     * @return boolean
     */
    public function provides($detailCode)
    {
        return in_array($detailCode, array(
            IDetail::DESC_SHORT, IDetail::DESC_LONG, IDetail::TITLE,
            IDetail::POSTER_SMALL, IDetail::POSTER_MEDIUM, IDetail::POSTER_LARGE));
    }
    
    /**
     * Add provider's data to a movie
     * @param string $language
     * @param \flims\movie\IMovie The movie to fill
     * @param \flims\movie\IDetail[]
     * @return \flims\movie\IMovie
     */
    public function fillMovie($language, IMovie $movie, array $details)
    {
    }
            
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
            throw new InvalidDefinitionException(sprintf('Invalid NYT listing definition: %s', $definition));
        }
        
        if (!isset($decodedDef['search'])) {
            throw new InvalidDefinitionException(sprintf('Invalid NYT listing definition, does not contain "search" key: %s', $definition));
        }
        
        //Simple search webservice
        $client = $this->getGuzzleClient();
        $command = $client->getCommand('ReviewsSearch', array('query' => $decodedDef['search']));
        $result = array();
        foreach ($client->execute($command)->getAll()['results'] as $item) {
            $result[] = MovieId::fromProvider(self::CODE, $item['nyt_movie_id']);
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
            $this->guzzleClient = GuzzleNytClient::factory(array(
                GuzzleNytClient::CURL_OPTIONS => $this->getCurlOptions(),
                'api-key' => self::$conf->get('nyt/api-key')));
        }
        
        return $this->guzzleClient;
    }
}