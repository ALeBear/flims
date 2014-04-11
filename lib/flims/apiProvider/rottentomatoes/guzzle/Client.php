<?php

namespace flims\apiProvider\rottenTomatoes\guzzle;

use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;

class Client extends GuzzleClient
{
    /**
     * Factory method to create a new rotten tomatoes client
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    oauth_consumer_key - Netflix API key
     *
     * @return Client
     */
    public static function factory($config = array())
    {
        isset($config[GuzzleClient::COMMAND_PARAMS]) || $config[GuzzleClient::COMMAND_PARAMS] = array();
        isset($config[GuzzleClient::COMMAND_PARAMS]['apikey']) || $config[GuzzleClient::COMMAND_PARAMS]['apikey'] = $config['apikey'];
        $client = new self('', $config);
        $client->setDescription(ServiceDescription::factory(__DIR__ . DIRECTORY_SEPARATOR . 'service_description.json'));
        
        return $client;
    }
}