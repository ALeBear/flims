<?php

require __DIR__ . '/bootstrap/general.php';

use flims\apiProvider\Directory;
use flims\apiProvider\allocine\ApiProvider as AllocineApiProvider;
use flims\movie\Movie;
use flims\movie\IDetail;
use flims\movie\detail\String as StringDetail;

$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
    
echo <<<EOF
<h2>Allocine movie search</h2>
<form>
<input type="text" name="q" value="$query"/> <input type="submit" value="Go"/>
</form>
EOF;

if ($query) {
    $allocine = Directory::getInstance()->locate(AllocineApiProvider::CODE);
    $movie = new Movie();
    $movie->addDetail(StringDetail::factory(IDetail::TITLE, 'en-EN')->setValue($query));
    $allocine->addIdToMovie($movie)->fillMovie('fr-FR', $movie, array(IDetail::TITLE));
    echo '<pre>';print_r($movie);
//    echo '<pre>';print_r(DynamicListing::fromDefinition(sprintf('{"provider": "%s", "definition": {"search": "%s"}}', ApiProvider::CODE, $query))->getMovieIds());
}
