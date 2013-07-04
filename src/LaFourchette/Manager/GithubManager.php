<?php

namespace LaFourchette\Manager;

/**
 * Description of GithubManager
 *
 * @author gcavana
 */
class GithubManager
{
    protected $client;

    public function __construct()
    {
        $this->client = new \Github\Client(
                new \Github\HttpClient\CachedHttpClient()
        );
        $this->client->authenticate('***REMOVED***', null, \Github\Client::AUTH_HTTP_TOKEN);
    }
    
    public function findRepositories()
    {
        return $this->client->api('repos')->find('lafourchette');
    }

}