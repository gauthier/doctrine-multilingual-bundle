<?php
namespace Gauthier\MultilingualBundle;

use Doctrine\DBAL\Types\Type;
use Gauthier\MultilingualString\Type\MultilingualStringType;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineMultilingualBundle extends Bundle
{
    public function __construct()
    {
        Type::addType('multilingual_string', MultilingualStringType::class);
    }


    public function getContainerExtension(): ?ExtensionInterface
    {
        return new DependencyInjection\DoctrineMultilingualExtension();
    }

}
