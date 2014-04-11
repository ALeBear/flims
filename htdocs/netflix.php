<?php

require __DIR__ . '/bootstrap/general.php';

use flims\movie\Builder;
use flims\movie\IDetail;
use flims\MovieId;
use flims\ServiceDirectory;

$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
    
echo <<<EOF
<h2>Netflix movie search</h2>
<form>
<input type="text" name="q" value="$query"/> <input type="submit" value="Go"/>
</form>
EOF;

if ($query) {
//    echo '<pre>';print_r(DynamicListing::fromDefinition(sprintf('{"provider": "%s", "definition": {"search": "%s"}}', ApiProvider::CODE, $query))->getMovieIds());
    
//    $result = $provider->getMovies(sprintf('{"search": "%s"}', str_replace('"', '\"', $query)));
//    foreach ($result as $movie) {
//        echo sprintf('<img src="%s"/> - %s - %s<br/><br/>',
//                $movie["box_art"]['@attributes']['medium'],
//                $movie["release_year"],
//                $movie["title"]["@attributes"]['regular']);
//    }
}

//$builder = Builder::start(MovieId::fromUuid('n207856'))
//    ->setName('test')
//    ->addProvider('n')
//    ->addDetail(IDetail::TITLE)
//    ->addDetail(IDetail::DESC_LONG);
//$em = ServiceDirectory::getInstance()->locate('EntityManager')->getConcreteService();
//$em->persist($builder);
//$em->flush();

$builder = Builder::fromName(MovieId::fromUuid('n207856'), 'test');

echo '<pre>';print_r($builder->end());






//$client = NetflixClient::factory(array('oauth_consumer_key' => $apiConf->get('netflix/consumer_key')));
//$command = $client->getCommand('Autocomplete', array('term' => 'test'));
//$result = $client->execute($command);
//print_r($result);
