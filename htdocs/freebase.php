<?php

require __DIR__ . '/bootstrap/general.php';

use flims\listing\DynamicListing;
use flims\apiprovider\freebase\ApiProvider;

$query = isset($_GET['q']) ? $_GET['q'] : '';
$queryHTML = htmlspecialchars($query);
    
echo <<<EOF
<h2>Freebase search</h2>
<form>
<input type="text" name="q" value="$queryHTML"/> <input type="submit" value="Go"/>
</form>
EOF;

if ($query) {
    $query = json_decode(sprintf('[{
  "id": "/en/2007_cannes_film_festival",
  "/type/object/name": [],
  "/type/reflect/any_reverse" : [
     {
       "guid" : null,
       "name" : null,
       "type" : "/film/film"
     }
   ]
}]'));
    echo '<pre>';print_r(DynamicListing::fromDefinition(json_encode(array("provider" => ApiProvider::CODE, "definition" => array("query" => $query))))->getMovieIds());
}
