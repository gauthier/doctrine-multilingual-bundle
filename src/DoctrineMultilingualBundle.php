<?php


namespace Gauthier\MultilingualBundle;


use Gauthier\MultilingualBundle\DependencyInjection\CompilerPass\DoctrineMultilingualCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineMultilingualBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new DependencyInjection\DoctrineMultilingualExtension();
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DoctrineMultilingualCompilerPass());
        parent::build($container);
    }

}
