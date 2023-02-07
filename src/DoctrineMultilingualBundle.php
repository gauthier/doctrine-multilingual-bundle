<?php


namespace Gauthier\MultilingualBundle;


use Gauthier\MultilingualBundle\DependencyInjection\CompilerPass\DoctrineMultilingualCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineMultilingualBundle extends Bundle
{

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new DependencyInjection\DoctrineMultilingualExtension();
    }

}
