<?php namespace EventListener;

use Gauthier\MultilingualBundle\EventListener\DoctrineMultilingualInitializer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineMultilingualInitializerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @dataProvider dataForTestAcceptLanguageParsing
     * @throws \ReflectionException
     */
    public function testAcceptLanguageParsing(string $header, array $sortedLanguages)
    {
        $container = $this->createMock(ContainerInterface::class);
        $initializer = new DoctrineMultilingualInitializer($container);

        $this->assertEquals($sortedLanguages, $initializer->sortPreferredLanguages($header));
    }

    public function dataForTestAcceptLanguageParsing()
    {
        return [
            ['fr-FR,fr;q=0.9,de-DE;q=0.8,de;q=0.7,en-US;q=0.6,en;q=0.5', ['fr-FR', 'fr', 'de-DE', 'de', 'en-US', 'en']]
        ];
    }
}
