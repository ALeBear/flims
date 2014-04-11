<?php

require __DIR__ . '/bootstrap/general.php';

use flims\listing\DynamicListing;
use flims\apiprovider\nyt\ApiProvider;

$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
    
echo <<<EOF
<h2>NY Times Reviews search</h2>
<form>
<input type="text" name="q" value="$query"/> <input type="submit" value="Go"/>
</form>
EOF;

if ($query) {
    echo '<pre>';print_r(DynamicListing::fromDefinition(sprintf('{"provider": "%s", "definition": {"search": "%s"}}', ApiProvider::CODE, $query))->getMovieIds());
}




//$apiConf = flims\Config::file('api');
//
//echo '<plaintext>';
//
//$client = RottenClient::factory(array('apikey' => $apiConf->get('rottentomatoes/apikey')));
//$command = $client->getCommand('MoviesSearch', array('q' => '2001'));
//$result = $client->execute($command);
//print_r($result);
