<?php

require_once __DIR__ . '/general.php';

use flims\IniConfig as FlimsConfig;
use flims\ServiceDirectory;
use flims\ServiceEntry;
use Doctrine\ORM\Tools\Setup as DoctrineSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;
use flims\Doctrine\Types\MovieIds;

$dbConf = FlimsConfig::file('db');

$config = DoctrineSetup::createAnnotationMetadataConfiguration(array(__DIR__ . "/../../lib"), true);
$config->setProxyDir(__DIR__ . '/../../lib/Proxies');
$config->setProxyNamespace('Proxies');

$conn = array(
    'driver' => $dbConf->get('Doctrine/driver'),
    'host' => $dbConf->get('mysql/host'),
    'dbname' => $dbConf->get('mysql/dbname'),
    'user' => $dbConf->get('mysql/username'),
    'password' => $dbConf->get('mysql/password'));

$em = EntityManager::create($conn, $config);
$em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
ServiceDirectory::getInstance()->register(new ServiceEntry('EntityManager', $em));
if (!Type::hasType(MovieIds::TYPENAME)) {
    Type::addType(MovieIds::TYPENAME, 'flims\Doctrine\Types\MovieIds');
}
