<?php

namespace flims\movie;

/**
 * Interface to movie detail
 */
interface IDetail
{
    const TITLE = 'title';
    const DESC_SHORT = 'short_description';
    const DESC_LONG = 'long_description';
    const POSTER_SMALL = 'small_poster';
    const POSTER_MEDIUM = 'medium_poster';
    const POSTER_LARGE = 'large_poster';
    const IMDB_ID = 'imdb_id';

    /**
     * @return string The language code used to define the detail
     */
    public function getLanguage();
    
    /**
     * @return string The code of the details
     */
    public function getCode();
    
    /**
     * @return string The provider code of the source
     */
    public function getProviderCode();
    
    /**
     * Is this detail filled by a movie provider?
     * @return boolean
     */
    public function isFilled();
    
    /**
     * @return string The string representation, sometimes it's what we show the
     * user
     */
    public function __toString();
}