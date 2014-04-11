<?php

namespace flims\movie;

use flims\MovieId;
use flims\IConfig;

/**
 * Interface for all the movie builders
 */
interface IBuilder
{
    /**
     * Injects config about default language and such
     * @param \flims\IConfig $conf
     */
    public static function injectConf(IConfig $conf);

    /**
     * @param \flims\MovieId $id
     * @return \flims\movie\IBuilder
     */
    public static function start(MovieId $id);

    /**
     * Returns a fully configured builder from a blueprint. Can still be
     * configured more if needed
     * @param \flims\MovieId $id
     * #param string $blueprint
     * @return \flims\movie\IBuilder
     */
    public static function fromBlueprint(MovieId $id, $blueprint);

    /**
     * ISO 639 ("en") or IETF ("en-US") language code
     * @param type $langCode
     * @return \flims\movie\IBuilder $this
     */
    public function setLanguage($langCode);
    
    /**
     * Adds a provider for the details
     * @param string $code
     * @return \flims\movie\IBuilder $this
     */
    public function addProvider($code);
    
    /**
     * Adds a detail type needed, and force its provider if wanted
     * @param string $detailCode
     * @param string $providerCode
     * @return \flims\movie\IBuilder $this
     */
    public function addDetail($detailCode, $providerCode = null);
    
    /**
     * @return string JSON-encoded blueprint storable
     */
    public function getBlueprint();
    
    /**
     * @return \flims\movie\Movie
     */
    public function end();
}
