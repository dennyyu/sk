<?php
namespace common\helpers;

use Exception;
use linslin\yii2\curl\Curl;

/**
 * Class RestHelper
 *
 * @package common\helpers
 */
class RestHelper
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * 调用外部REST API.数据格式Json
     *
     * @param      $method
     * @param      $url
     * @param null $body
     * @param null $header
     *
     * @return mixed|null
     * @throws Exception
     */
    public static function callRestAPIJson($method, $url, $body = null, $header = null)
    {
        $curl     = new Curl();
        $response = null;
        if (isset($header)) {
            $curl->setOptions($header);
        }

        $method = strtoupper($method);

        if ($method == self::METHOD_GET) {
            $response = $curl->get($url);
        } elseif ($method == self::METHOD_POST) {
            $response = $curl->setOption(
                CURLOPT_POSTFIELDS,
                $body
            )->post($url);
        } elseif ($method == self::METHOD_PUT) {
            $response = $curl->setOption(
                CURLOPT_POSTFIELDS,
                $body
            )->put($url);
        } elseif ($method == self::METHOD_DELETE) {
            $response = $curl->delete($url);
        } else {
            throw new Exception("Not support method.");
        }

        return $response;
    }

    /**
     * 调用外部REST API.数据格式Form
     *
     * @param      $method
     * @param      $url
     * @param null $body
     * @param null $header
     *
     * @return mixed|null
     * @throws Exception
     */
    public static function callRestAPIForm($method, $url, $body = null, $header = null)
    {
        $curl     = new Curl();
        $response = null;
        if (isset($header)) {
            $curl->setOptions($header);
        }

        $method = strtoupper($method);

        if ($method == self::METHOD_GET) {
            $response = $curl->get($url);
        } elseif ($method == self::METHOD_POST) {
            $response = $curl->setOption(
                CURLOPT_POSTFIELDS,
                http_build_query($body)
            )->post($url);
        } elseif ($method == self::METHOD_PUT) {
            $response = $curl->setOption(
                CURLOPT_POSTFIELDS,
                http_build_query($body)
            )->put($url);
        } elseif ($method == self::METHOD_DELETE) {
            $response = $curl->delete($url);
        } else {
            throw new Exception("Not support method.");
        }

        return $response;
    }

    public static function getWithForm($url, $body)
    {
        return self::callRestAPIForm(self::METHOD_GET, $url . '?' . http_build_query($body));
    }

    public static function postWithForm($url, $body, $header = null)
    {
        return self::callRestAPIForm(self::METHOD_POST, $url, $body, $header);
    }

    public static function putWithForm($url, $body, $header = null)
    {
        return self::callRestAPIForm(self::METHOD_PUT, $url, $body, $header);
    }

    public static function postWithJson($url, $body, $header = null)
    {
        return self::callRestAPIJson(self::METHOD_POST, $url, $body, $header);
    }

    public static function putWithJson($url, $body, $header = null)
    {
        return self::callRestAPIJson(self::METHOD_PUT, $url, $body, $header);
    }
}
