<?php


namespace Gauthier\MultilingualBundle\EventListener;


use Gauthier\MultilingualString\MultilingualString;
use Gauthier\MultilingualString\MultilingualStringException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class DoctrineMultilingualInitializer
{

    /**
     * @var ContainerInterface
     */
    protected $parameters;

    /**
     * DoctrineMultilingualInitializer constructor.
     * @param ContainerInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param RequestEvent $event
     * @throws MultilingualStringException
     */
    public function onKernelRequest(RequestEvent $event): void
    {

        // set available languages
        MultilingualString::setAvailableLanguages($this->parameters->get('doctrine_mutlilingual.languages'));

        // set fallback language
        $fallbackLanguage = $this->parameters->get('doctrine_mutlilingual.fallback');
        if ($fallbackLanguage == 'auto') {
            $fallbackLanguage = $this->negotiateFallbackLanguage($event->getRequest());
            if (!$fallbackLanguage) {
                $fallbackLanguage = $event->getRequest()->getDefaultLocale();
            }
        }

        MultilingualString::setFallbackLanguage($fallbackLanguage);

        $defaultLanguage = $this->parameters->get('doctrine_mutlilingual.default');
        if ($defaultLanguage == 'auto') {
            // look for http Accept-Language header
            $defaultLanguage = $this->negotiateDefaultLanguage($event->getRequest());
        }

        MultilingualString::setDefaultLanguage($defaultLanguage);


        foreach ($this->parameters->get('doctrine_mutlilingual.routes') as $route) {
            $route = array_values($route);
            MultilingualString::setRoute(...$route);
        }
    }

    protected function negotiateFallbackLanguage(Request $request)
    {
        $frameworkFallbackLocales = $this->parameters->get('translator')->getFallbackLocales();
        if ($frameworkFallbackLocales) {
            return $frameworkFallbackLocales[0];
        }

    }

    /**
     * @param Request $request
     * @return mixed
     * @throws MultilingualStringException
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
