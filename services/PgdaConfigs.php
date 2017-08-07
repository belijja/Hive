<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 07.06.2017
 * Time: 15:47
 */
declare(strict_types = 1);

namespace Services;

use Containers\ServiceContainer;

class PgdaConfigs extends ServiceContainer
{


    private $pgdaPrefixCasinoCodesPrimary = [
        'create'         => 40002,
        //if you want to switch to default configs comment this primary value and put null on that place
        'createTest'     => 40000000,
        'transaction'    => 42001,
        'delete'         => 50001,
        'deleteTest'     => 50000000,
        'history'        => 105820,
        'sessionBalance' => 59000
    ];

    private $pgdaPrefixCasinoCodesDefault = [
        'create'         => 40000,
        'createTest'     => 40000000,
        'transaction'    => 42000,
        'delete'         => 50000,
        'deleteTest'     => 50000000,
        'history'        => 1058000,
        'sessionBalance' => 59000
    ];

    private $pgdaServerPrimary = [//primary and default are the same?
        'scheme'         => 'http',
        'address'        => '81.2.205.160',
        'port'           => '80',
        'path'           => '/GiochiDiAbilitaV2_1/ServletFactoryFirma_V213_',
        'cashPath'       => 'SH',
        'tournamentPath' => 'TR',
        'casinoPath'     => 'QF',
        '580Path'        => '580',
        '780Path'        => '780'
    ];

    private $pgdaServerDefault = [
        'scheme'         => 'http',
        'address'        => '81.2.205.160',
        'port'           => '80',
        'path'           => '/GiochiDiAbilitaV2_1/ServletFactoryFirma_V213_',
        'cashPath'       => 'SH',
        'tournamentPath' => 'TR',
        'casinoPath'     => 'QF',
        '580Path'        => '580',
        '780Path'        => '780'
    ];

    private $pgdaAamsPrimary = [
        'conc'         => 15242,
        'fsc'          => 70,
        'maxSendTries' => 3
    ];
    private $pgdaAamsDefault = [
        'conc'         => 15079,
        'fsc'          => 89,
        'maxSendTries' => 3
    ];

    private $pgdaCertificatesPrimary = [
        'private'         => 'pgda/Certificates/firma-2.pem',
        'privatePassword' => 'sks_test',
        'sogeiPublic'     => 'pgda/Certificates/sogei.cer'
    ];
    private $pgdaCertificatesDefault = [
        'private'         => 'pgda/Certificates/firma_sks_test.pem',
        'privatePassword' => 'sks_test',
        'sogeiPublic'     => 'pgda/Certificates/sogei.cer'
    ];

    private $pgdaPrefixPrimary = [
        'updateSessionEnd' => 81000,
        'sendModuleList'   => 83000,
        'retry'            => 42424242
    ];

    private $pgdaPrefixDefault = [
        'updateSessionEnd' => 81000,
        'sendModuleList'   => 83000,
        'retry'            => 42424242
    ];

    /**
     * @param string $pgdaKey
     * @return string
     */
    public function getPgdaPrefix(string $pgdaKey): string
    {
        return ($this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaPrefixPrimary) != '') ? $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaPrefixPrimary) : $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaPrefixDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public function getPgdaCertificates(string $pgdaKey): string
    {
        return ($this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaCertificatesPrimary) != '') ? $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaCertificatesPrimary) : $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaCertificatesDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public function getPgdaAamsCodes(string $pgdaKey): string
    {
        return ($this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaAamsPrimary) != '') ? $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaAamsPrimary) : $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaAamsDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public function getPgdaServerCodes(string $pgdaKey): string
    {
        return ($this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaServerPrimary) != '') ? $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaServerPrimary) : $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaServerDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public function getPgdaCasinoCodes(string $pgdaKey): string
    {
        return ($this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaPrefixCasinoCodesPrimary) != '') ? $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaPrefixCasinoCodesPrimary) : $this->container->get('Config')->checkIfKeyExists((string)$pgdaKey, $this->pgdaPrefixCasinoCodesDefault);
    }
}