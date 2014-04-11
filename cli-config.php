<?php

require __DIR__ . '/htdocs/bootstrap/doctrine.php';

use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\DBAL\Types\Type;
use flims\Doctrine\Types\MovieIds;
use flims\ServiceDirectory;

$helperSet = new HelperSet(array(
//    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
    'em' => new EntityManagerHelper(ServiceDirectory::getInstance()->locate('EntityManager')->getConcreteService())
));

ServiceDirectory::getInstance()->locate('EntityManager')->getConcreteService()->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('db_' . MovieIds::TYPENAME, MovieIds::TYPENAME);

