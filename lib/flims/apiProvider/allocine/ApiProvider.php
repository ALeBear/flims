<?php

namespace flims\apiProvider\allocine;

use flims\IDirectoryEntry;
use flims\MovieId;
use flims\ProviderMovieIdMissingException;
use flims\movie\IDetail;
use flims\movie\detail\String as StringDetail;
use flims\movie\detail\Image as ImageDetail;
use flims\movie\IMovie;
use flims\movie\TitleMissingException;
use flims\apiProvider\TApiProvider;
use flims\apiProvider\IProvider;
use flims\apiProvider\IMovieProvider;
use flims\apiProvider\MovieNotFoundException;
use flims\apiProvider\allocine\guzzle\Client as GuzzleAllocineClient;


class ApiProvider implements IProvider, IMovieProvider, IDirectoryEntry
{
    use TApiProvider;
    
    const CODE = 'a';
        
    /**
     * @var flims\apiProvider\allocine\guzzle\Client
     */
    protected $guzzleClient = null;
    
    
    /**
     * Do this provider have stuff for this language?
     * @param string $langCode (ISO 639 or IETF)
     * @return boolean
     */
    public function providesForLanguage($langCode)
    {
        return substr($langCode, 0, 2) == 'fr';
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
     * Gets Allocine's id for the given movie (should contain a title detail)
     * @param \flims\movie\IMovie $movie
     * @return \flims\apiProvider\allocine\ApiProvider $this
     */
    public function addIdToMovie(IMovie $movie)
    {
        //Is it already there?
        try {
            $movie->getId()->getId(self::CODE);
            return $this;
        } catch (ProviderMovieIdMissingException $e) {}
        
        //Not there. Is there a title to search? We can search by original title
        if (!($title = $movie->getDetail(IDetail::TITLE))) {
            throw new TitleMissingException(sprintf('Allocine needs a title to find an allocine movie id'));
        }
        
        //Search by title and assume it's the first result
        $client = $this->getGuzzleClient();
        $command = $client->getCommand('Search', array('q' => $title));
        $result = $client->execute($command)->getAll();
        if (!isset($result['feed']['movie'][0]['code'])) {
            throw new MovieNotFoundException(sprintf('Allocine cannot find movie with title: "%s"', $title));
        }
        
        $movie->getId()->addFromProvider(self::CODE, $result['feed']['movie'][0]['code']);
        
        return $this;
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
        $command = $client->getCommand('Detail', array('code' => $movie->getId()->getId(self::CODE)));
        $result = $client->execute($command)->getAll();
        foreach ($details as $detail) {
            switch ($detail) {
                case IDetail::TITLE:
                    $detail = StringDetail::factory($detail, $language)->setValue($result['movie']['title'], self::CODE);
                    break;
                case IDetail::DESC_SHORT:
                    $detail = StringDetail::factory($detail, $language)->setValue($result['movie']['synopsisShort'], self::CODE);
                    break;
                case IDetail::DESC_LONG:
                    $detail = StringDetail::factory($detail, $language)->setValue($result['movie']['synopsis'], self::CODE);
                    break;
                case IDetail::POSTER_SMALL:
                case IDetail::POSTER_MEDIUM:
                case IDetail::POSTER_LARGE:
                    $detail = ImageDetail::factory($detail, $language)->setValue($result['movie']['poster']['href'], self::CODE);
                    break;
            }
            $movie->addDetail($detail);
        }
        
        return $movie;
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
            $this->guzzleClient = GuzzleAllocineClient::factory(array(
                GuzzleAllocineClient::CURL_OPTIONS => $this->getCurlOptions(),
                'partner' => self::$conf->get('allocine/partner')));
        }
        
        return $this->guzzleClient;
    }
}