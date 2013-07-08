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
    protected $projects;

    public function __construct()
    {
        $projects = array(
            'lafourchette-rr',
            'lafourchette-core',
            'lafourchette-portal',
            'lafourchette-bo',
            'lafourchette-b2b');
        
        $this->projects = $projects;

        $this->client = new \Github\Client(
                new \Github\HttpClient\CachedHttpClient()
        );
        $this->client->authenticate('***REMOVED***', null, \Github\Client::AUTH_HTTP_TOKEN);
    }

    public function getAllRepositoriesWithBranch()
    {
        $repositories = array();
        foreach($this->projects as $project)
        {
            $branches =  $this->client->api('repo')->branches('lafourchette', $project);
            if(!empty($branches))
            {
                foreach($branches as $branch)
                {
                    $repositories[$project][] = $branch['name'];
                }
                
            }
            
        }
   
        return $repositories;
    }

}