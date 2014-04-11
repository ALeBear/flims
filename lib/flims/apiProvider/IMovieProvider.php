<?php

namespace flims\apiProvider;

use flims\movie\IMovie;

/**
 * Movie provider interface
 */
interface IMovieProvider
{
    /**
     * Do this provider have stuff for this language?
     * @param string $langCode (ISO 639 or IETF)
     * @return boolean
     */
    public function providesForLanguage($langCode);
    
    /**
     * Do this provider have this detail?
     * @param string $detailCode
     * @return boolean
     */
    public function provides($detailCode);
    
    /**
     * Add provider's data to a movie
     * @param string $language
     * @param \flims\movie\IMovie The movie to fill
     * @param \flims\movie\IDetail[]
     * @return \flims\movie\IMovie
     */
    public function fillMovie($language, IMovie $movie, array $details);
}