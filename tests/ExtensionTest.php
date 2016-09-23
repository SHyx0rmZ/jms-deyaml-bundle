<?php

namespace SHyx0rmZ\JMSDeYamlBundle\Test;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use SHyx0rmZ\JMSDeYamlBundle\DependencyInjection\JMSDeYamlExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ExtensionTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function setUp() {
        $container = new ContainerBuilder();

        $extension = new JMSDeYamlExtension();
        $extension->load([], $container);

        $namingStrategy = $this->prophesize(PropertyNamingStrategyInterface::class)->reveal();
        $objectConstructor = $this->prophesize(ObjectConstructorInterface::class)->reveal();

        $container->set('jms_serializer.naming_strategy', $namingStrategy);
        $container->set('jms_serializer.object_constructor', $objectConstructor);

        $container->compile();

        $this->container = $container;
    }

    public function testAddsDefinitionToContainer() {
        $definition = $this->container->getDefinition('shyxormz.jms_deyaml.yaml_deserialization_visitor');

        $this->assertInstanceOf(Definition::class, $definition);
    }

    public function testIsTaggedAsVisitor() {
        $definition = $this->container->getDefinition('shyxormz.jms_deyaml.yaml_deserialization_visitor');

        $this->assertTrue($definition->hasTag('jms_serializer.deserialization_visitor'));
    }

    public function testTagSpecifiesFormat() {
        $definition = $this->container->getDefinition('shyxormz.jms_deyaml.yaml_deserialization_visitor');

        $tag = $definition->getTag('jms_serializer.deserialization_visitor');

        $this->assertArrayHasKey('format', $tag[0]);
    }

    public function testFormatIsYml() {
        $definition = $this->container->getDefinition('shyxormz.jms_deyaml.yaml_deserialization_visitor');

        $tag = $definition->getTag('jms_serializer.deserialization_visitor');

        $this->assertEquals('yml', $tag[0]['format']);
    }
}
