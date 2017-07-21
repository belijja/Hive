<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 21.04.2017
 * Time: 14:17
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

use Containers\ServiceContainer;
use Models\ServiceModels\IServiceModels;

/**
 * Class SoapManager
 * @package Helpers\SoapHelpers
 */
class SoapManager
{

    use ServiceContainer;

    /**
     * @param IServiceModels $modelObject
     */
    public function soapToPost(IServiceModels $modelObject)
    {
        $result = '';
        foreach ($modelObject as $kk => $vv) {
            $result .= $this->fieldToUrl("" . $kk, $vv);
        }
        echo $result;
        exit;
    }

    /**
     * @param array $modelArray
     */
    public function soapArrayToPost(array $modelArray)
    {
        $result = '';
        $result .= "r" . "_cnt=" . count($modelArray) . "&";
        foreach ($modelArray as $kk => $vv) {
            $result .= $this->fieldToUrl('r' . "[$kk]", $vv);
        }
        echo $result;
        exit;
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    private function fieldToUrl(string $key, string $value): string
    {
        return rawurlencode($key) . "=" . rawurlencode($value) . "&";
    }

    /**
     * @param string $namespace
     * @return string
     */
    public function namespaceToWsdlFilename(string $namespace): string
    {
        $url = parse_url($namespace);
        $fileName = $this->container->get('Config')->getWsdl('wsdlCacheDir') . $url['host'] . str_replace([
                '/',
                '.php'
            ], [
                '-',
                ''
            ], $url['path']) . ".xml";
        return $fileName;
    }
}