<?php

namespace flims\apiProvider\tmdb;

use flims\IDirectoryEntry;
use flims\movie\IDetail;
use flims\movie\IMovie;
use flims\apiProvider\TApiProvider;
use flims\apiProvider\IProvider;
use flims\apiProvider\IMovieProvider;
use flims\apiProvider\tmdb\guzzle\Client as GuzzleTmdbClient;


class ApiProvider implements IProvider, IMovieProvider, IDirectoryEntry
{
    use TApiProvider;
    
    const CODE = 't';
        
    /**
     * @var flims\apiProvider\tmdb\guzzle\Client
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
            $this->guzzleClient = GuzzleTmdbClient::factory(array(
                GuzzleTmdbClient::CURL_OPTIONS => $this->getCurlOptions(),
                'api_key' => self::$conf->get('tmdb/api_key')));
        }
        
        return $this->guzzleClient;
    }
}