<?php

namespace LaFourchette\Provisioner\Shell;

/**
 * Downloads a provisionning file from github.
 */
class GithubFile
{
    private $repo;
    private $path;
    private $token;

    public function __construct($repo,$path,$token)
    {
        $this->repo = $repo;
        $this->path = $path;
        $this->token = $token;
    }

    public function getContent()
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,sprintf('https://api.github.com/repos/%s/%s', $this->repo, $this->path));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: token '.$this->token,
            'Accept: application/vnd.github.v3.raw'
        ));
        $content = curl_exec($ch);
        if($err = curl_error($ch)){
            throw new \Exception('Curl error'.$err);
        }
        curl_close($ch);
        return $content;
    }
} 