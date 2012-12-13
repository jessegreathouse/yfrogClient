<?php

namespace Jessegreathouse\Yfrog;

class YfrogRequest
{

    /**
     * Default connection and response timeout
     */
    const YFROG_API_TIMEOUT = 10;

    protected $media;

    protected $pub = 'yes';

    protected $url;

    protected $message;

    protected $source;

    protected $tags = array();

    protected $timeout;

    public function __construct()
    {
        $this->timeout = self::YFROG_API_TIMEOUT;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function setMedia($media)
    {
        if (0 !== strpos($media, '@')) $media = '@'.$media;
        $this->media = $media;
        return $this;
    }

    public function getPub()
    {
        return $this->pub;
    }

    public function setPub(boolean $pub)
    {
        $this->pub = $pub ? 'yes' : 'no';
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags;
        return $this;
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function getTimeout()
    {
        $this->timeout;
    }

    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }
}
