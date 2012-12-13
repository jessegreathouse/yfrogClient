<?php

namespace Jessegreathouse\Yfrog;

use Jessegreathouse\Yfrog\YfrogObj as Yfrog;
use Jessegreathouse\Yfrog\YfrogRequest;

class YfrogClient
{
    /**
     * API endpoint
     */
    const YFROG_API_URL = 'http://yfrog.com/api';

    /**
     * Error codes,
     * 3XXX are client errors
     * 2XXX are ImageShack-specified errors
     * 1XXX are standard errors
     */
    const YFROG_ERROR_BAD_CREDENTIALS =       1001;
    const YFROG_ERROR_NO_FILE =               1002;
    const YFROG_ERROR_SIZE_TOO_BIG =          1004;
    const YFROG_ERROR_INVALID_REQUEST =       1005;

    const YFROG_ERROR_WRONG_ACTION =          2001;
    const YFROG_ERROR_UPLOAD_FAILED =         2002;
    const YFROG_ERROR_STATUS_UPDATE_FAILED =  2003;

    const YFROG_ERROR_IO_ERROR =              3001;
    const YFROG_ERROR_MALFORMED_XML =         3002;

    protected $username;

    protected $password;

    protected $key;

    protected $request;

    public function __set($name, $value) 
    {
        $this->request->{'set' . ucwords($name)}($value);
    }

    public function __get($name)
    {
        $this->request->{'get' . ucwords($name)}();
    }

    public function __construct($username, $password, $key, YfrogRequest $request = null) {
        $this->username = $username;
        $this->password = $password;
        $this->key = $key;
        if (null === $request) {
            $request = new YfrogRequest();
        }
        $this->request = $request;
    } 

    public function getRequest()
    {
        return $this->request;
    }
    
    public function setRequest(YfrogRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Uploads file to yfrog.com. 
     * Requirements: simplexml, curl
     * @param Jessegreathouse\Yfrog\YfrogRequest $request
     * @return Jessegreathouse\Yfrog\YfrogObj
     *  stat: true/false; true indicates success, false - error
     *  code: error code  (only if stat = false)
     *  msg: error message (only if stat = false)
     *  mediaid media identifier (only if stat = true)
     *  mediaurl media URL (only if stat = true)
     */
    public function upload(YfrogRequest $request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }
        return $this->exec('upload');
    }

    /**
     * Transloads file to yfrog.com. 
     * Requirements: simplexml, curl
     * @param Jessegreathouse\Yfrog\YfrogRequest $request
     * @return Jessegreathouse\Yfrog\YfrogObj
     *  stat: true/false; true indicates success, false - error
     *  code: error code  (only if stat = false)
     *  msg: error message (only if stat = false)
     *  mediaid media identifier (only if stat = true)
     *  mediaurl media URL (only if stat = true)
     */
    public function transload(YfrogRequest $request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }
        return $this->exec('upload');
    }

    /**
     * Uploads file to yfrog.com and posts message to Twitter. 
     * Requirements: simplexml, curl
     * @param Jessegreathouse\Yfrog\YfrogRequest $request
     * @return Jessegreathouse\Yfrog\YfrogObj
     *  stat: true/false; true indicates success, false - error
     *  code: error code  (only if stat = false)
     *  msg: error message (only if stat = false)
     *  mediaid media identifier (only if stat = true)
     *  mediaurl media URL (only if stat = true)
     */
    public function uploadAndPost(YfrogRequest $request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }
        return $this->exec('uploadAndPost');
    }

    /**
     * Transloads file to yfrog.com and posts message to Twitter. 
     * Requirements: simplexml, curl
     * @param Jessegreathouse\Yfrog\YfrogRequest $request
     * @return Jessegreathouse\Yfrog\YfrogObj
     *  stat: true/false; true indicates success, false - error
     *  code: error code  (only if stat = false)
     *  msg: error message (only if stat = false)
     *  mediaid media identifier (only if stat = true)
     *  mediaurl media URL (only if stat = true)
     */
    public function transloadAndPost(YfrogRequest $request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }
        return $this->exec('uploadAndPost');
    }


    protected function loadRequest()
    {
        return array(
            'username' => $this->username,
            'password' => $this->password,
            'tags'     => implode(',', $this->request->getTags()),
            'public'   => $this->request->getPub(),
            'url'      => $this->request->getUrl(),
            'media'    => $this->request->getMedia(),
            'message'  => $this->request->getMessage(),
            'source'   => $this->request->getSource(),
            'key'      => $this->key
        );
    }

    protected function exec($action)
    {
        $yfrog = new Yfrog;
        $yfrog->setClient($this);

        if (!is_a($this->request , 'YfrogRequest')) {
            $yfrog->setStat(false)
                  ->setCode(YFROG_ERROR_INVALID_REQUEST)
                  ->setMsg('Invalid request object')
                  ;
            return $yfrog;
        }

        $handle = @curl_init(self::YFROG_API_URL . '/'. $action);

        if (!$handle) {
            $yfrog->setStat(false)
                  ->setCode(YFROG_ERROR_IO_ERROR)
                  ->setMsg('Unable to initialize CURL')
                  ;
            return $yfrog;
        }

        $timeout = $this->request->getTimeout();

        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $this->loadRequest($request));

        $response = curl_exec($handle);

        $error = curl_errno($handle);
        if ($error)
        {
            $yfrog->setStat(false)
                  ->setCode(YFROG_ERROR_IO_ERROR)
                  ->setMsg(curl_error($handle) . ' [' . $error . ']')
                  ;
            curl_close($handle);
            return $yfrog;
        }

        curl_close($handle);

        $xml = @simplexml_load_string($response);
        if (!$xml) {
            $yfrog->setStat(false)
                  ->setCode(YFROG_ERROR_MALFORMED_XML)
                  ->setMsg('Malformed XML is received as response')
                  ;
            return $yfrog;
        }

        if (@$xml->attributes()->stat == 'fail') {
            $yfrog->setStat(false)
                  ->setCode($xml->err->attributes()->code)
                  ->setMsg($xml->err->attributes()->msg)
                  ;
            return $yfrog;
        } elseif (@$xml->attributes()->stat == 'ok') {
            $yfrog->setStat(true)
                  ->setMediaId($xml->mediaid)
                  ->setMediaUrl($xml->mediaurl)
                  ->setStatusId(@$xml->statusid)
                  ->setUserId(@$xml->userid)
                  ;
            return $yfrog;
        } else {
            $yfrog->setStat(false)
                  ->setCode(YFROG_ERROR_MALFORMED_XML)
                  ->setMsg('Unexpected XML is received as response')
                  ;
        }
        return $yfrog;
    }
}
?>
