<?php

require __DIR__ . '/bootstrap/general.php';

use flims\apiProvider\rottentomatoes\ApiProvider;
use flims\listing\DynamicListing;

$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
    
echo <<<EOF
<h2>Rotten Tomatoes movie search</h2>
<form>
<input type="text" name="q" value="$query"/> <input type="submit" value="Go"/>
</form>
EOF;

if ($query) {
    echo '<pre>';print_r(DynamicListing::fromDefinition(sprintf('{"provider": "%s", "definition": {"search": "%s"}}', ApiProvider::CODE, $query))->getMovieIds());

    
//    $client = Directory::getInstance()->locate(ApiProvider::CODE)->getGuzzleClient();
//    $command = $client->getCommand('MoviesSearch', array('q' => $query));
//    $result = $client->execute($command);
//    foreach ($result['movies'] as $movie) {
//        echo sprintf('<img src="%s"/> - %s - %s<br/><br/>',
//                $movie["posters"]['profile'],
//                $movie["year"],
//                $movie["title"]);
//    }
}




//$apiConf = flims\Config::file('api');
//
//echo '<plaintext>';
//
//$client = RottenClient::factory(array('apikey' => $apiConf->get('rottentomatoes/apikey')));
//$command = $client->getCommand('MoviesSearch', array('q' => '2001'));
//$result = $client->execute($command);
//print_r($result);
