<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

class ObjectToIdTransformerTest extends TestCase
{
    public function testValidTransformation() : void
    {
        $object = new TestObject();
        $object->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => $object,
            'findBy'    => [$object],
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, TestObject::class, 'name');

        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals($object, $reversed);
    }

    public function testInvalidValidTransformation() : void
    {
        $object = new TestObject();
        $object->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => null,
            'findBy'    => [null],
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, TestObject::class, 'name');

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Can\'t find entity of class "Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject" with property "name" = "test".');

        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals(null, $reversed);
    }

    public function testInvalidProperty() : void
    {
        $object = new TestObject();
        $object->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => null,
            'findBy'    => [null],
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $this->expectException(NoSuchPropertyException::class);
        $this->expectExceptionMessage('property id is missing in class Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject');

        new ObjectToIdTransformer($registry, TestObject::class, 'id');
    }
}
