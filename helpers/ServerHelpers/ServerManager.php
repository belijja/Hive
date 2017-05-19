<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 04.05.2017
 * Time: 14:54
 */
declare(strict_types = 1);

namespace Helpers\ServerHelpers;

use Helpers\ConfigHelpers\ConfigManager;

class ServerManager
{
    private $postParamsMaxLengths = [

        "InsertPokerRegistration" => [
            "firstName" => 50,
            "lastName"  => 50,
            "city"      => 50,
            "street"    => 199,
            "state"     => 50,
            "country"   => 49,
            "zip"       => 15,
            "dob"       => 11,
            "phone"     => 25,
        ]

    ];

    /**
     * @param string $functionName
     * @param array $postParams
     * @param null $url
     * @return array
     * @throws \SoapFault
     */
    public function callExternalMethod(string $functionName, array $postParams, $url = null): array
    {
        if ($url == null) {
            $url = "http://" . ConfigManager::getServer('address') . ":" . (ConfigManager::getServer('nePort') != null ? ConfigManager::getServer('nePort') : 8002) . "/";
        }
        $postParams = $this->checkAndFixPostParams($functionName, $postParams);
        $encoded = http_build_query($postParams) . "&" . urlencode("action") . "=" . urlencode($functionName);
        return $this->sendPostParams($url, $encoded);
    }

    /**
     * @param string $url
     * @param string $encodedString
     * @return array
     * @throws \SoapFault
     */
    private function sendPostParams(string $url, string $encodedString): array
    {
        $channel = curl_init($url);
        $options = [
            CURLOPT_POSTFIELDS     => $encodedString,
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($channel, $options);
        /*$response = curl_exec($channel);
        if(!$response) {
            $response['status'] = false;
            return $response;
        }*/
        $response = 'status=1&pokerId=32';
        curl_close($channel);
        $result = [];
        parse_str($response, $result);
        return $result;
    }

    /**
     * @param string $functionName
     * @param array $postParams
     * @return array
     */
    private function checkAndFixPostParams(string $functionName, array $postParams): array
    {
        if (!isset($this->postParamsMaxLengths) || !isset($this->postParamsMaxLengths[$functionName])) {
            return $postParams;
        }
        $return = [];
        foreach ($postParams as $k => $v) {
            if (!isset($this->postParamsMaxLengths[$functionName][$k]) || strlen((string)$v) <= $this->postParamsMaxLengths[$functionName][$k]) {
                $return[$k] = $v;
            }
        }
        return $return;
    }

}