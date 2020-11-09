<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Form\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestFormModel;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

/**
 * @covers \Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectToIdTransformer
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
        $reversed    = $transformer->reverseTransform('test');

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
        // phpcs:disable Generic.Files.LineLength.TooLong
        $this->expectExceptionMessage('Can\'t find entity of class "Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject" with property "name" = "test".');
        // phpcs:enable Generic.Files.LineLength.TooLong

        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals(null, $reversed);
    }

    public function testInvalidProperty(): void
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

    public function testInvalidObject(): void
    {
        $object = new TestFormModel();

        $registry = $this->createMock(ManagerRegistry::class);

        $transformer = new ObjectToIdTransformer($registry, TestObject::class, 'name');

        $this->expectException(InvalidArgumentException::class);
        // phpcs:disable Generic.Files.LineLength.TooLong
        $this->expectExceptionMessage('Expected an instance of Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject. Got: Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestFormModel');
        // phpcs:enable Generic.Files.LineLength.TooLong

        $transformer->transform($object);
    }

    public function testInvalidArray(): void
    {
        $object = new TestFormModel();

        $registry = $this->createMock(ManagerRegistry::class);

        $transformer = new ObjectToIdTransformer($registry, TestObject::class, 'name');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject. Got: array');

        $transformer->transform([$object]);
    }

    public function testInvalidClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an existing class name. Got: "FakeClass"');

        new ObjectToIdTransformer($registry, 'FakeClass', 'name');
    }
}
