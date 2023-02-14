<?php


namespace Gauthier\MultilingualBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MultilingualService
{

    /** @var ContainerInterface */
    protected $container;

    /** @var ObjectManager */
    protected $objectManager;

    /**
     * MultilingualService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, ManagerRegistry $registry)
    {
        $this->container = $container;
        $this->objectManager = $registry->getManager();
    }

    public function getMultilingualFields()
    {
        $entities = [];
        $manager = $this->getObjectManager();
        $meta = $manager->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            $entities[] = $m->getName();
        }

        return $entities;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }


}
