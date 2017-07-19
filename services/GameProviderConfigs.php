<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 22.05.2017
 * Time: 14:05
 */
declare(strict_types = 1);

namespace Services;

use Containers\ServiceContainer;

class GameProviderConfigs
{
    use ServiceContainer;

    private $gameProviderConfigs = [
        'tpg'         => [
            'password'     => 'tpgpass',
            'providerId'   => 9,
            'tokenTimeout' => 300,
        ],
        'playson'     => [
            'password'     => 'Johb5gahp',
            'providerId'   => 9,
            'tokenTimeout' => 300,
        ],
        'betsoft'     => [
            'password'     => 'yaek8Ieb',
            'providerId'   => 14,
            'tokenTimeout' => 300,
        ],
        'f1x2'        => [
            'password'     => 'qui6Eboo',
            'providerId'   => 16,
            'tokenTimeout' => 300,
        ],
        'isoftbet'    => [
            'password'     => 'cujeePhah6',
            'providerId'   => 17,
            'tokenTimeout' => 300,
        ],
        'gameart'     => [
            'password'     => 'ei5Le8lieG',
            'providerId'   => 19,
            'tokenTimeout' => 300,
        ],
        'netent'      => [
            'password'     => 'Ohs1eeno',
            'providerId'   => 26,
            'tokenTimeout' => 300,
        ],
        'portomaso'   => [
            'password'     => 'HD2t36mz',
            'providerId'   => 28,
            'tokenTimeout' => 300,
        ],
        'luckystreak' => [
            'password'     => 'hZb0WHn2',
            'providerId'   => 29,
            'tokenTimeout' => 300,
        ],
        'medialive'   => [
            'password'     => 'aer2Jeir',
            'providerId'   => 27,
            'tokenTimeout' => 300,
        ],
        'xpro'        => [
            'password'     => 'tryGNf8c',
            'providerId'   => 30,
            'tokenTimeout' => 300,
        ]
    ];

    public function getGameProviderConfigs(int $providerName = null): array
    {
        return !is_null($providerName) ? $this->container->get('Config')->checkIfArrayExists((string)$providerName, $this->gameProviderConfigs) : $this->gameProviderConfigs;
    }
}