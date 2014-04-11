<?php

namespace flims\apiProvider\netflix\guzzle;

use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Plugin\Oauth\OauthPlugin;

class Client extends GuzzleClient
{
    const AUTH_NONE = 'noauth';
    const AUTH_SIGNED = 'signed';
    const AUTH_PROTECTED = 'protected';
    
    protected static $OAUTH_PARAMS = array('oauth_consumer_key', 'oauth_consumer_secret', 'oauth_token', 'oauth_token_secret');
    
    /**
     * Factory method to create a new netflix client
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    oauth_consumer_key - Netflix API key
     *
     * @return Client
     */
    public static function factory($config = array())
    {
        $client = new self('', $config);
        $client->setDescription(ServiceDescription::factory(__DIR__ . DIRECTORY_SEPARATOR . 'service_description.json'));

        return $client;
    }
    
    /**
     * Get a command by name. Overloaded here to check for OAuth config and add
     * a OAuth Plugin to the client if necessary
     *
     * @return CommandInterface
     * @throws InvalidArgumentException if no command can be found by name
     */
    public function getCommand($name, array $args = array())
    {
        switch ($authType = $this->getCommandAuthentication($name)) {
            case self::AUTH_NONE:
                //Non-Auth commands, just add the consumer key in the QS
                $required = array('oauth_consumer_key');
                isset($args['oauth_consumer_key']) || $args['oauth_consumer_key'] = $this->getConfig('oauth_consumer_key');
                break;
            case self::AUTH_PROTECTED:
                //Protected methods
                $required = self::$OAUTH_PARAMS;
                break;
            case self::AUTH_SIGNED:
                //Signed methods 
                $required = array('oauth_consumer_key', 'oauth_consumer_secret');
        }
        $config = Collection::fromConfig($this->getConfig()->getAll(), array(), $required)->getAll();
        
        if ($authType != self::AUTH_NONE) {
            $this->addOAuthPlugin($config);
        }
        
        return parent::getCommand($name, $args);
    }
    
    /**
     * Add a OAuthPlugin to this client with params taken from the given config
     * @param array $config
     * @return \flims\guzzle\netflix\Client
     */
    protected function addOAuthPlugin(array $config)
    {
        $pluginParams = array();
        foreach (self::$OAUTH_PARAMS as $param) {
            if (isset($config[$param])) {
                $pluginParams[substr($param, strlen('oauth_'))] = $config[$param];
            }
        }
        $oauth = new OauthPlugin($pluginParams);
        $this->addSubscriber($oauth);
        
        return $this;
    }
    
    /**
     * Get teh authentication method depending on the method's name
     * @param type $name
     * @return string
     */
    protected function getCommandAuthentication($name)
    {
        switch ($name) {
            case "Autocomplete":
                return self::AUTH_NONE;
            case "TEST":
                return self::AUTH_PROTECTED;
            default:
                return self::AUTH_SIGNED;;
        }
    }
}