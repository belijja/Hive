<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 22.05.2017
 * Time: 10:32
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

use Helpers\ConfigHelpers\ConfigManager;

class NetentSoapClient
{
    public function getSoapClient()
    {
        return new \SoapClient(ConfigManager::getNetent('apiLocation'), [
            'trace'              => 1,
            'connection_timeout' => ConfigManager::getNetent('apiConnectionTimeout')
        ]);
    }
}