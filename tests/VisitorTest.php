<?php

namespace SHyx0rmZ\JMSDeYamlBundle\Test;

use JMS\Serializer\SerializerInterface;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use JMS\SerializerBundle\JMSSerializerBundle;
use SHyx0rmZ\JMSDeYamlBundle\DependencyInjection\JMSDeYamlExtension;
use SHyx0rmZ\JMSDeYamlBundle\JMSDeYamlBundle;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VisitorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function setUp() {
        $bundles = [
            new FrameworkBundle(),
            new JMSSerializerBundle(),
            new JMSDeYamlBundle(),
        ];
        $extensions = [
            new FrameworkExtension(),
            new JMSSerializerExtension(),
            new JMSDeYamlExtension(),
        ];

        $container = new ContainerBuilder();

        $container->setParameter('kernel.bundles', $bundles);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'visitor-test' . DIRECTORY_SEPARATOR . 'cache');
        $container->setParameter('kernel.charset', 'UTF-8');
        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.root_dir', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'visitor-test');
        $container->setParameter('kernel.secret', 'change me');

        foreach ($bundles as $bundle) {
            $bundle->build($container);
        }

        foreach ($extensions as $extension) {
            $extension->load([], $container);
        }

        $container->compile();

        $this->container = $container;
    }

    public function testSerializerCanDeserializeYaml() {
        /**
         * @var SerializerInterface $serializer
         */
        $serializer = $this->container->get('jms_serializer');

        $expected = new \DateTime('2016-09-23 17:45:00', new \DateTimeZone('UTC'));
        $string = $serializer->serialize($expected, 'yml');
        $actual = $serializer->deserialize($string, 'DateTime', 'yml');

        $this->assertInstanceOf('DateTime', $expected);
        $this->assertInternalType('string', $string);
        $this->assertInstanceOf('DateTime', $actual);
        $this->assertEquals($expected, $actual);
    }
}
