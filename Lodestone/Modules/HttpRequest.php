<?php

namespace Lodestone\Modules;

/**
 * Class HttpRequest
 * @package src\Modules
 */
class HttpRequest
{
    /**
     * curl options
     */
    protected $CURL_OPTIONS = [
        CURLOPT_POST => false,
        CURLOPT_BINARYTRANSFER => false,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FORBID_REUSE => true,
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2TLS,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_HTTPHEADER => ['Content-type: text/html; charset=utf-8', 'Accept-Language: en'],
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ];
    
    const HTTP_OK = 200;
    const HTTP_PERM_REDIRECT = 308;
    const HTTP_SERVICE_NOT_AVAILABLE = 503;
    const HTTP_NOT_FOUND = 404;
    
    public function __construct($useragent = "")
    {
        if (!empty($useragent)) {
            $this->CURL_OPTIONS[CURLOPT_USERAGENT] = $useragent;
        }
    }
    
  /**
   * @param $url
   * @return bool|string
   * @throws ValidationException
   */
    public function get($url)
    {
        $url = str_ireplace(' ', '+', $url);

        // build handle
        $handle = curl_init();
        curl_setopt_array($handle, $this->CURL_OPTIONS);
        curl_setopt($handle, CURLOPT_URL, $url);

        // handle response
        $response = curl_exec($handle);
        $hlength = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $data = substr($response, $hlength);

        curl_close($handle);
        unset($handle);

        // specific conditions to return code on
        if ($httpCode == self::HTTP_NOT_FOUND) {
            throw new \Exception('Requested page was not found', $httpCode);
        } elseif ($httpCode == self::HTTP_SERVICE_NOT_AVAILABLE) {
            throw new \Exception('Lodestone not available', $httpCode);
        } elseif ($httpCode < self::HTTP_OK || $httpCode > self::HTTP_PERM_REDIRECT) {
            throw new \Exception('Requested page is not available', $httpCode);
        }
        
         
        // check that data is not empty
        if (empty($data)) {
            throw new \Exception('Requested page is empty');
        }

        return $data;
    }
    
    public function check($object, $name, $id = null)
    {
        $this->object = $object;
        $this->name = $name;
        $this->id = $id;
        return $this;
    }

    /**
     * @return $this
     */
    public function isNotEmpty()
    {
        if (empty($this->object)) {
            throw Exceptions::emptyValidation($this);
        }

        return $this;
    }
}
?>