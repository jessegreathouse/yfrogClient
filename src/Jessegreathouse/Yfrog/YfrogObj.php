<?php

namespace Jessegreathouse\Yfrog;

class YfrogObj
{
    protected $stat;

    protected $mediaId;

    protected $mediaUrl;

    protected $statusId;

    protected $userId;

    protected $msg;

    protected $code;

    protected $client;

    public function getClient()
    {
        return unserialize(base64_decode($this->client));
    }

    public function setClient($client)
    {
        $this->client = base64_encode(serialize($client));
        return $this;
    }

    public function getStat()
    {
        return $this->stat;
    }

    public function setStat($stat)
    {
        $this->stat = $stat;
        return $this;
    }

    public function getMediaId()
    {
        return $this->mediaId;
    }

    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
        return $this;
    }

    public function getMediaUrl()
    {
        return $this->mediaUrl;
    }

    public function setMediaUrl($mediaUrl)
    {
        $this->mediaUrl = $mediaUrl;
        return $this;
    }

    public function getStatusId()
    {
        return $this->statusId;
    }

    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}
