<?php

namespace LaFourchette\Model;

class Prototype
{
    protected $activity;
    protected $lastBuildTime;
    protected $lastBuildLabel;
    protected $lastBuildStatus;
    protected $name;
    protected $webUrl;


    /**
     * setName
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * setWebUrl
     * @param string $webUrl
     */
    public function setWebUrl($webUrl)
    {
        $this->webUrl = $webUrl;
        return $this;
    }

    /**
     * setActivity
     * @param string $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * setLastBuildTime
     * @param string $lastBuildTime
     */
    public function setLastBuildTime($lastBuildTime)
    {
        $this->lastBuildTime = $lastBuildTime;
        return $this;
    }

    /**
     * setLastBuildLabel
     * @param string $lastBuildLabel
     */
    public function setLastBuildLabel($lastBuildLabel)
    {
        $this->lastBuildLabel = $lastBuildLabel;
        return $this;
    }

    /**
     * setLastBuildStatus
     * @param string $lastBuildStatus
     */
    public function setLastBuildStatus($lastBuildStatus)
    {
        $this->lastBuildStatus = $lastBuildStatus;
        return $this;
    }

    /**
     * getName
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getWebUrl
     * @return string
     */
    public function getWebUrl()
    {
        return $this->webUrl;
    }

    /**
     * getActivity
     * @return string
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * getLastBuildTime
     * @return string
     */
    public function getLastBuildTime()
    {
        return $this->lastBuildTime;
    }

    /**
     * getLastBuildLabel
     * @return string
     */
    public function getLastBuildLabel()
    {
        return $this->lastBuildLabel;
    }

    /**
     * getLastBuildStatus
     * @return string
     */
    public function getLastBuildStatus()
    {
        return $this->lastBuildStatus;
    }

}
