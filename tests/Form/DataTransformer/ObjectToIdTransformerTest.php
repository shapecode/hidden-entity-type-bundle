<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class ObjectToIdTransformerTest
 */
class ObjectToIdTransformerTest extends TestCase
{

    public function testValidTransformation(): void
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
        $reversed = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals($object, $reversed);
    }

    public function testInvalidValidTransformation(): void
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
        $reversed = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals(null, $reversed);
    }
}
