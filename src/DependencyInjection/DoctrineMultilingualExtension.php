<?php


namespace Gauthier\MultilingualBundle\DependencyInjection;


use Gauthier\MultilingualBundle\Controller\MultilingualStringsController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DoctrineMultilingualExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('doctrine_mutlilingual.languages', $config['languages'] ?: ['en']);
        $container->setParameter('doctrine_mutlilingual.default', $config['default']);
        $container->setParameter('doctrine_mutlilingual.fallback', $config['fallback']);
        $container->setParameter('doctrine_mutlilingual.routes', $config['routes'] ?? []);


    }


}
