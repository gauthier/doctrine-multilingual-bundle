<?php


namespace Gauthier\MultilingualBundle\EventListener;


use Gauthier\MultilingualString\MultilingualString;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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
     * @param GetResponseEvent $event
     * @throws \Gauthier\MultilingualString\MultilingualStringException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {

        // set available languages
        MultilingualString::setAvailableLanguages($this->container->getParameter('doctrine_mutlilingual.languages'));

        // set fallback language
        $fallbackLanguage = $this->container->getParameter('doctrine_mutlilingual.fallback');
        if ($fallbackLanguage == 'auto') {
            $fallbackLanguage = $this->negotiateFallbackLanguage($event->getRequest());
            if (!$fallbackLanguage) {
                $fallbackLanguage = $event->getRequest()->getDefaultLocale();
            }
        }

        MultilingualString::setFallbackLanguage($fallbackLanguage);

        $defaultLanguage = $this->container->getParameter('doctrine_mutlilingual.default');
        if ($defaultLanguage == 'auto') {
            // look for http Accept-Language header
            $defaultLanguage = $this->negotiateDefaultLanguage($event->getRequest());
        }

        MultilingualString::setDefaultLanguage($defaultLanguage);


        foreach ($this->container->getParameter('doctrine_mutlilingual.routes') as $route) {
            $route = array_values($route);
            MultilingualString::setRoute(...$route);
        }
    }

    protected function negotiateFallbackLanguage(Request $request)
    {
        $frameworkFallbackLocales = $this->container->get('translator')->getFallbackLocales();
        if ($frameworkFallbackLocales) {
            return $frameworkFallbackLocales[0];
        }

    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Gauthier\MultilingualString\MultilingualStringException
     */
    protected function negotiateDefaultLanguage(Request $request)
    {
        // first, fetch acceptable languages according to browser
        $acceptLanguageHeader = $request->headers->get('Accept-Language');
        $preferredLanguages = $this->sortPreferredLanguages($acceptLanguageHeader);

        // look for framework usr language
        $language = $request->getLocale();

        array_unshift($preferredLanguages, $language);

        foreach ($preferredLanguages as $language) {
            if (in_array($language, MultilingualString::getAvailableLanguages())) {
                return $language;
            }
        }

        // no preferred language is available, return first available language
        return MultilingualString::getFallbackLanguage() ?: MultilingualString::getAvailableLanguages()[0];
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
}
