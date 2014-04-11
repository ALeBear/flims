<?php

use flims\IniConfig as FlimsConfig;
use flims\apiProvider\Directory;
use flims\apiProvider\netflix\ApiProvider as NetflixApiProvider;
use flims\apiProvider\freebase\ApiProvider as FreebaseApiProvider;
use flims\apiProvider\nyt\ApiProvider as NytApiProvider;
use flims\apiProvider\rottentomatoes\ApiProvider as RottenApiProvider;
use flims\apiProvider\tmdb\ApiProvider as TmdbApiProvider;
use flims\apiProvider\allocine\ApiProvider as AllocineApiProvider;

$conf = FlimsConfig::file('api');
$directory = Directory::getInstance();
NetflixApiProvider::injectConf($conf);
NetflixApiProvider::registerToDirectory($directory);
FreebaseApiProvider::injectConf($conf);
FreebaseApiProvider::registerToDirectory($directory);
NytApiProvider::injectConf($conf);
NytApiProvider::registerToDirectory($directory);
RottenApiProvider::injectConf($conf);
RottenApiProvider::registerToDirectory($directory);
TmdbApiProvider::injectConf($conf);
TmdbApiProvider::registerToDirectory($directory);
AllocineApiProvider::injectConf($conf);
AllocineApiProvider::registerToDirectory($directory);
