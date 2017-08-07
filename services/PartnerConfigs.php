<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 25.05.2017
 * Time: 16:37
 */
declare(strict_types = 1);

namespace Services;

use Containers\ServiceContainer;

class PartnerConfigs extends ServiceContainer
{
    public $x = 0;

    private $partnerConfigs = [
        'pw'         => [
            'password'   => 'pwpass',
            'providerId' => 2,
        ],
        'isibet'     => [
            'password'   => 'X5Plk4MB',
            'providerId' => 1001,
        ],
        'vintagames' => [
            'password'   => 'Pae7ial3',
            'providerId' => 1002,
        ],
        'misterbet'  => [
            'password'   => 'fuThohg6',
            'providerId' => 1003,
        ]
    ];

    public function getPartnerConfigs($partnerName)
    {
        error_log("Number " . $this->x);
        $this->x++;
        return $this->container->get('Config')->checkIfArrayExists((string)$partnerName, $this->partnerConfigs);
    }
}