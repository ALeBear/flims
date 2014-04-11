<?php

namespace flims\apiProvider;

/**
 * Listing provider interface
 */
interface IListingProvider
{
    /**
     * Get the movies corresponding to a definition
     * @param string $definition
     * @param integer $offsetStart
     * @param integer $offsetLength
     * @return \flims\MovieId[]
     */
    public function getMovieIds($definition, $offsetStart, $offsetLength);
}