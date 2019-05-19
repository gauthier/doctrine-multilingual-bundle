<?php


namespace Gauthier\MultilingualBundle\DependencyInjection;


use Gauthier\MultilingualString\MultilingualString;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DoctrineMultilingualBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('default.yml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        MultilingualString::setAvailableLanguages($config['doctrine_multilingual']['languages']['supported']);
        MultilingualString::setDefaultLanguage($config['doctrine_multilingual']['languages']['default']);
        MultilingualString::setFallbackLanguage($config['doctrine_multilingual']['languages']['fallback']);

        if($routes = $config['doctrine_multilingual']['routes']) {
            foreach($routes as $from => $to) {
                MultilingualString::setRoute($from, $to);
            }
        }

    }

}
