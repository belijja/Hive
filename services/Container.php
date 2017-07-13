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

class Container
{
    /**
     * @return ContainerBuilder
     */
    public function getService(): ContainerBuilder
    {
        /*$container = new ContainerBuilder();
        $container->register('Logger', 'Helpers\LogHelpers\LogManager')->setShared(true);
        $container->register('Db', 'Helpers\ConfigHelpers\Db')->setShared(true);
        return $container;*/
    }
}





