<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Webmozart\Assert\Assert;

use function in_array;
use function sprintf;

/**
 * @template TKey
 * @template T
 * @template-implements  DataTransformerInterface<TKey, T>
 */
abstract class Transformer implements DataTransformerInterface
{
    protected ManagerRegistry $registry;

    /** @var class-string */
    protected string $class;

    protected string $property;

    /**
     * @param string<class-string> $class
     */
    public function __construct(
        ManagerRegistry $registry,
        string $class,
        string $property = 'id'
    ) {
        Assert::classExists($class);

        $this->registry = $registry;
        $this->class    = $class;
        $this->property = $property;

        $this->validate();
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->registry->getRepository($this->getClass());
    }

    /**
     * @return class-string
     */
    protected function getClass(): string
    {
        return $this->class;
    }

    protected function getProperty(): string
    {
        return $this->property;
    }

    protected function validate(): void
    {
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo        = new PropertyInfoExtractor([$reflectionExtractor]);

        $properties = $propertyInfo->getProperties($this->class) ?? [];

        if (! in_array($this->property, $properties, true)) {
            throw new NoSuchPropertyException(sprintf('property %s is missing in class %s', $this->property, $this->class));
        }
    }
}
