<?php

namespace flims\apiProvider\allocine\guzzle;

use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;

class Client extends GuzzleClient
{
    /**
     * Factory method to create a new allocine client
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    oauth_consumer_key - Netflix API key
     *
     * @return Client
     */
    public static function factory($config = array())
    {
        isset($config[GuzzleClient::COMMAND_PARAMS]) || $config[GuzzleClient::COMMAND_PARAMS] = array();
        isset($config[GuzzleClient::COMMAND_PARAMS]['partner']) || $config[GuzzleClient::COMMAND_PARAMS]['partner'] = $config['partner'];
        $client = new self('', $config);
        $client->setDescription(ServiceDescription::factory(__DIR__ . DIRECTORY_SEPARATOR . 'service_description.json'));
        
        return $client;
    }
}