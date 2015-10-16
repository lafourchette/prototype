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
    private $user;

    public function __construct($repo,$path,$token, $user)
    {
        $this->repo = $repo;
        $this->path = $path;
        $this->token = $token;
        $this->user = $user;
    }

    public function getContent()
    {
        $url = sprintf('https://api.github.com/repos/%s/contents/%s', $this->repo, $this->path);
        echo $url . PHP_EOL . "( headers: Authorization: token" . $this->token . "User-Agent: ".$this->user . " )";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: token '.$this->token,
            'Accept: application/vnd.github.v3.raw',
            'User-Agent: '.$this->user
        ));
        $content = curl_exec($ch);
        if ($err = curl_error($ch)) {
            throw new \Exception('Curl error'.$err);
        }else{
            echo "curl sucessfull."
        }
        curl_close($ch);

        return $content;
    }
}
