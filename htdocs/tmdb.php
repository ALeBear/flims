<?php

require __DIR__ . '/bootstrap/general.php';

use flims\apiProvider\Directory;
use flims\apiprovider\tmdb\ApiProvider;

$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
    
echo <<<EOF
<h2>TMDb movie search</h2>
<form>
<input type="text" name="q" value="$query"/> <input type="submit" value="Go"/>
</form>
EOF;

if ($query) {
    $client = Directory::getInstance()->locate(ApiProvider::CODE)->getGuzzleClient();
    $command = $client->getCommand('MoviesSearch', array('query' => $query));
    $result = $client->execute($command);
//    echo '<pre>';print_r($result);
    foreach ($result['results'] as $movie) {
        echo sprintf('<img src="http://cf2.imgobject.com/t/p/w90%s"/> - %s - %s<br/><br/>',
                $movie["poster_path"],
                substr($movie["release_date"], 0, 4),
                $movie["title"]);
    }
}




//$apiConf = flims\Config::file('api');
//
//echo '<plaintext>';
//
//$client = RottenClient::factory(array('apikey' => $apiConf->get('rottentomatoes/apikey')));
//$command = $client->getCommand('MoviesSearch', array('q' => '2001'));
//$result = $client->execute($command);
//print_r($result);
