<?php

require __DIR__ . '/bootstrap/general.php';

use flims\listing\DiscreteListing;
use flims\ServiceDirectory;
use flims\MovieId;

$list = new DiscreteListing();
$list->setName('bar');
$list->addMovieId(MovieId::fromUuid('n1234'));
$list->addMovieId(MovieId::fromUuid('n4321'));
/* @var $em \Doctrine\ORM\EntityManager */
$em = ServiceDirectory::getInstance()->locate('EntityManager')->getConcreteService();
$em->persist($list);
$em->flush();

$list = $em->find('flims\listing\DiscreteListing', $id);
echo '<pre>';print_r($list);

