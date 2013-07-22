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
    protected $projectManager;
    protected $githubToken;

    public function __construct(ProjectManager $projectManager, $githubToken)
    {
        $this->projectManager = $projectManager;

        $this->client = new \Github\Client(
                new \Github\HttpClient\CachedHttpClient()
        );
        $this->githubToken = $githubToken;
        $this->client->authenticate($this->githubToken, null, \Github\Client::AUTH_HTTP_TOKEN);
    }

    public function getAllRepositoriesWithBranch()
    {
        $projects = $this->projectManager->loadAll();

        $repositories = array();
        foreach ($projects as $key => $project) {
            $branches = $this->client->api('repo')->branches('lafourchette', $project->getName());
            $repositories[$key]['name'] = $project->getName();
            $repositories[$key]['id'] = $project->getIdProject();
            if (!empty($branches)) {
                foreach ($branches as $branch) {
                    $repositories[$key]['branches'][$branch['name']] = $branch['name'] !== 'master' ? null : 'selected';
                }
            }
        }
        return $repositories;
    }

}