<?php


namespace Gauthier\MultilingualBundle;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineMultilingualBundle extends Bundle
{



    public function getContainerExtension()
    {
       return new DependencyInjection\DoctrineMultilingualExtension();
    }


}
