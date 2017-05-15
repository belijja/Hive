<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 11.05.2017
 * Time: 15:36
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

class SoapProxy extends \SoapClient
{
    private $tpiConfig;

    /**
     * SoapProxy constructor.
     * @param array $tpiConfig
     */
    public function __construct(array $tpiConfig)
    {
        $this->tpiConfig = $tpiConfig;
    }

    public function __call($methodName, $params)
    {
        $postCurlParams = $params[0];
        $postCurlParams['method'] = $methodName;
        if (isset($this->tpiConfig['user'])) {
            $postCurlParams['login'] = $this->tpiConfig['user'];
            $postCurlParams['password'] = $this->tpiConfig['password'];
        }
        $headers = [
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        ];
        $channel = curl_init($this->tpiConfig['postUrl']);
        $options = [
            CURLOPT_POSTFIELDS     => http_build_query($postCurlParams),
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($channel, $options);
        $response = curl_exec($channel);
        $transferInfo = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        curl_close($channel);
        if ($response === false) {
            return new \SoapFault("1", "HTTP CODE: " . $transferInfo);
        }
        $newName = $methodName . "Result";
        $return = new \stdClass();
        $return->$newName = new \stdClass();

        $parsed = [];
        parse_str($return, $parsed);
        foreach ($parsed as $key => $value) {
            $return->$newName->$key = $value;
        }
        if (!isset($return->$newName->resultCode)) {
            return new \SoapFault("2", 'ResultCode not set!');
        }
        return $return;
    }
}