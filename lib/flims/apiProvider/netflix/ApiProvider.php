<?php

namespace flims\apiProvider\netflix;

use flims\IDirectoryEntry;
use flims\movie\IDetail;
use flims\movie\IMovie;
use flims\MovieId;
use flims\movie\detail\String as StringDetail;
use flims\movie\detail\Image as ImageDetail;
use flims\listing\InvalidDefinitionException;
use flims\apiProvider\TApiProvider;
use flims\apiProvider\IProvider;
use flims\apiProvider\IListingProvider;
use flims\apiProvider\IMovieProvider;
use flims\apiProvider\netflix\guzzle\Client as GuzzleNetflixClient;

class ApiProvider implements IProvider, IListingProvider, IMovieProvider, IDirectoryEntry
{
    use TApiProvider;
    
    const CODE = 'n';
        
    /**
     * @var flims\apiProvider\netflix\guzzle\Client
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
        $client = $this->getGuzzleClient();
        $baseResult = $descResult = null;
        foreach ($details as $detail) {
            switch ($detail) {
                case IDetail::TITLE:
                case IDetail::POSTER_SMALL:
                case IDetail::POSTER_MEDIUM:
                case IDetail::POSTER_LARGE:
                    if (!$baseResult) {
                        $command = $client->getCommand('MovieDetails', array('id' => $movie->getId()->getId(self::CODE)));
                        $baseResult = json_decode(json_encode($client->execute($command)), true);
                    }
                    break;
                case IDetail::DESC_SHORT:
                case IDetail::DESC_LONG:
                    if (!$descResult) {
                        $command = $client->getCommand('MovieSynopsis', array('id' => $movie->getId()->getId(self::CODE)));
                        $descResult = strip_tags((string) $client->execute($command));
                    }
                    break;
            }
        }
        foreach ($details as $detail) {
            switch ($detail) {
                case IDetail::TITLE:
                    $detail = StringDetail::factory($detail, $language)->setValue($baseResult['title']['@attributes']['regular'], self::CODE);
                    break;
                case IDetail::DESC_SHORT:
                case IDetail::DESC_LONG:
                    $detail = StringDetail::factory($detail, $language)->setValue($descResult, self::CODE);
                    break;
                case IDetail::POSTER_SMALL:
                    $detail = ImageDetail::factory($detail, $language)->setValue($baseResult['box_art']['@attributes']['small'], self::CODE);
                    break;
                case IDetail::POSTER_MEDIUM:
                    $detail = ImageDetail::factory($detail, $language)->setValue($baseResult['box_art']['@attributes']['medium'], self::CODE);
                    break;
                case IDetail::POSTER_LARGE:
                    $detail = ImageDetail::factory($detail, $language)->setValue($baseResult['box_art']['@attributes']['large'], self::CODE);
                    break;
            }
            $movie->addDetail($detail);
        }
        
        return $movie;
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
            throw new InvalidDefinitionException(sprintf('Invalid Netflix listing definition: %s', $definition));
        }
        
        if (!isset($decodedDef['search'])) {
            throw new InvalidDefinitionException(sprintf('Invalid Netflix listing definition, does not contain "search" key: %s', $definition));
        }
        
        //Simple search webservice
        $client = $this->getGuzzleClient();
        $command = $client->getCommand('SearchTitles', array('term' => $decodedDef['search']));
        $result = array();
        foreach (json_decode(json_encode($client->execute($command)), true)['catalog_title'] as $item) {
            $result[] = MovieId::fromProvider(self::CODE, $item['id']);
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
            $this->guzzleClient = GuzzleNetflixClient::factory(array(
                GuzzleNetflixClient::CURL_OPTIONS => $this->getCurlOptions(),
                'oauth_consumer_key' => self::$conf->get('netflix/consumer_key'),
                'oauth_consumer_secret' => self::$conf->get('netflix/consumer_secret')));
        }
        
        return $this->guzzleClient;
    }
}