<?php

require __DIR__ . '/../../vendor/autoload.php';
ComposerAutoloaderInit::getLoader()->add('flims', __DIR__ . '/../../lib');

use flims\IniConfig;
$env = 'dev';
IniConfig::injectEnv($env);
IniConfig::injectPath(realpath(__DIR__ . '/../../conf'));

use flims\movie\Builder;
Builder::injectConf(IniConfig::file('movie'));
