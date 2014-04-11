<?php

namespace flims\listing;

/**
 * Interface to all listings
 */
interface IListing
{
    const DEFAULT_LENGTH = 20;
    
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @param integer $start
     * @param integer $offset
     * @return \flims\MovieId[]
     */
    public function getMovieIds($offsetStart = 0, $offsetLength = self::DEFAULT_LENGTH);
}