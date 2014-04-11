<?php

namespace flims\listing;

use flims\MovieId;

/**
 * Listing comprised of pointers to movies by ids stored in DB as given by a
 * user
 * @Entity
 */
class ManualListing extends AbstractListing
{
    /**
     * @Column(type="movieids")
     * @var array
     */
    protected $movieIds;
    
    
    /**
     * @param \flims\FilmId $id
     * @return DiscreteList $this
     */
    public function addMovieId(MovieId $id)
    {
        if (!is_array($this->movieIds)) {
            $this->movieIds = array();
        }
        
        $this->movieIds[] = $id;
        return $this;
    }
    
    /**
     * @param integer $start
     * @param integer $offset
     * @return \flims\MovieId[]
     */
    public function getMovieIds($offsetStart = 0, $offsetLength = self::DEFAULT_LENGTH)
    {
        if ($offsetStart < 0 || $offsetLength < 1) {
            throw new InvalidOffsetException();
        }
        
        return array_slice($this->movieIds, $offsetStart, $offsetLength);
    }
}