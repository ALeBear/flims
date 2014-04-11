<?php

namespace flims\apiProvider\nyt\guzzle;

use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;

class Client extends GuzzleClient
{
    /**
     * Factory method to create a new NYT critics client
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    oauth_consumer_key - Netflix API key
     *
     * @return Client
     */
    public static function factory($config = array())
    {
        isset($config[GuzzleClient::COMMAND_PARAMS]) || $config[GuzzleClient::COMMAND_PARAMS] = array();
        isset($config[GuzzleClient::COMMAND_PARAMS]['api-key']) || $config[GuzzleClient::COMMAND_PARAMS]['api-key'] = $config['api-key'];
        $client = new self('', $config);
        $client->setDescription(ServiceDescription::factory(__DIR__ . DIRECTORY_SEPARATOR . 'service_description.json'));
        
        return $client;
    }
}