<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 13.7.17.
 * Time: 11.56
 */
declare(strict_types = 1);

namespace Services;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Container
{
    /**
     * @return ContainerBuilder
     */
    public function getService(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        //helpers
        $container->register('SoapManager', 'Helpers\SoapHelpers\SoapManager');
        $container->register('ParamManager', 'Helpers\SoapHelpers\ParamManager');
        $container->register('SessionManager', 'Helpers\SoapHelpers\SessionManager');
        //partners
        $container->register('SKSSoapClient', 'Helpers\SoapHelpers\SKSSoapClient');
        $container->register('ServerManager', 'Helpers\ServerHelpers\ServerManager');
        $container->register('Logger', 'Helpers\LogHelpers\LogManager');
        $container->register('Db', 'Helpers\ConfigHelpers\Db');
        $container->register('ThirdPartyIntegrationSoapClient', 'Helpers\SoapHelpers\ThirdPartyIntegrationSoapClient');
        $container->register('SKSPartner', 'Partners\SKSPartner')->setArguments([
            new Reference('SKSSoapClient'),
            new Reference('Db'),
            new Reference('Logger'),
            new Reference('ServerManager')
        ]);
        $container->register('ThirdPartyIntegrationPartner', 'Partners\ThirdPartyIntegrationPartner')->setArguments([
            new Reference('ThirdPartyIntegrationSoapClient'),
            new Reference('Db'),
            new Reference('Logger'),
            new Reference('ServerManager')
        ]);
        //users
        $container->register('ServiceUser', 'Users\ServiceUser')->addArgument(new Reference('Db'));
        //backoffice
        $container->register('Core', 'BackOffice\Core')->addArgument(new Reference('Db'));

        return $container;
    }
}





