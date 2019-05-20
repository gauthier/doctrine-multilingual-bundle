<?php


namespace Gauthier\MultilingualBundle\EventListener;


use Gauthier\MultilingualString\MultilingualString;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class DoctrineMultilingualInitializer
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * DoctrineMultilingualInitializer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        MultilingualString::setAvailableLanguages($this->container->getParameter('doctrine_mutlilingual.languages'));

        $defaultLanguage = $this->container->getParameter('doctrine_mutlilingual.default');
        if ($defaultLanguage == 'auto') {
            // look for http Accept-Language header
            $defaultLanguage = $this->negotiateDefaultLanguage($event->getRequest()->headers->get('Accept-Language'));
        }

        MultilingualString::setDefaultLanguage($defaultLanguage);
        MultilingualString::setFallbackLanguage($this->container->getParameter('doctrine_mutlilingual.fallback'));

        foreach ($this->container->getParameter('doctrine_mutlilingual.routes') as $route) {
            $route = array_values($route);
            MultilingualString::setRoute(...$route);
        }
    }

    public function sortPreferredLanguages($acceptLanguageHeader)
    {
        $acceptLanguages = explode(',', $acceptLanguageHeader);
        $sortedLanguages = [];
        foreach ($acceptLanguages as $lang) {
            $parts = explode(';', $lang);
            $langId = $parts[0];
            $weight = $parts[1] ?? null;

            if ($weight) {
                $weight = explode('=', $weight)[1];
            } else {
                $weight = 1;
            }
            $sortedLanguages[$langId] = $weight;
        }


        arsort($sortedLanguages);

        return array_keys($sortedLanguages);

    }

    protected function negotiateDefaultLanguage($acceptLanguageHeader)
    {
        $preferredLanguages = $this->sortPreferredLanguages($acceptLanguageHeader);

        foreach ($preferredLanguages as $language) {
            if(in_array($language, MultilingualString::getAvailableLanguages())) return $language;
        }

        // no preferred language is available, return first available language
        return MultilingualString::getAvailableLanguages()[0];
    }
}
