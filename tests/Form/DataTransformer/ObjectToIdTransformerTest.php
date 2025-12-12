<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Form\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

#[CoversClass(ObjectToIdTransformer::class)]
class ObjectToIdTransformerTest extends TestCase
{
    public function testValidTransformation(): void
    {
        $object = new TestObject();
        $object->setName('test');

        // mock any dependencies
        $objectRepository = self::createConfiguredStub(ObjectRepository::class, [
            'findOneBy' => $object,
            'findBy'    => [$object],
        ]);

        $registry = self::createConfiguredStub(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, TestObject::class, 'name');

        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertSame('test', $transformed);
        self::assertEquals($object, $reversed);
    }

    public function testInvalidValidTransformation(): void
    {
        $object = new TestObject();
        $object->setName('test');

        // mock any dependencies
        $objectRepository = self::createConfiguredStub(ObjectRepository::class, [
            'findOneBy' => null,
            'findBy'    => [null],
        ]);

        $registry = self::createConfiguredStub(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, TestObject::class, 'name');

        $this->expectException(TransformationFailedException::class);
        // phpcs:disable Generic.Files.LineLength.TooLong
        $this->expectExceptionMessage('Can\'t find entity of class "Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject" with property "name" = "test".');
        // phpcs:enable Generic.Files.LineLength.TooLong

        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertSame('test', $transformed);
        self::assertEquals(null, $reversed);
    }

    public function testInvalidProperty(): void
    {
        $object = new TestObject();
        $object->setName('test');

        // mock any dependencies
        $objectRepository = self::createConfiguredStub(ObjectRepository::class, [
            'findOneBy' => null,
            'findBy'    => [null],
        ]);

        $registry = self::createConfiguredStub(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $this->expectException(NoSuchPropertyException::class);
        $this->expectExceptionMessage('property id is missing in class Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject');

        new ObjectToIdTransformer($registry, TestObject::class, 'id');
    }

    public function testInvalidClass(): void
    {
        $registry = self::createStub(ManagerRegistry::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an existing class name. Got: "FakeClass"');

        // @phpstan-ignore-next-line
        new ObjectToIdTransformer($registry, 'FakeClass', 'name');
    }
}
