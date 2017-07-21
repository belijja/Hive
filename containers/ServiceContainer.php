<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 19.7.17.
 * Time: 09.39
 */
declare(strict_types = 1);

namespace Containers;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

trait ServiceContainer
{
    public $container;

    public function __construct()
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/'));
        $loader->load('containerConfig.yml');
    }
}