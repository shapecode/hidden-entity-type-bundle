<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use function explode;
use function implode;
use function in_array;
use function is_iterable;
use function sprintf;

class ObjectToIdTransformer implements DataTransformerInterface
{
    /** @var ManagerRegistry */
    protected $registry;

    /** @var string */
    protected $class;

    /** @var string */
    protected $property;

    /** @var bool */
    protected $multiple = false;

    public function __construct(
        ManagerRegistry $registry,
        string $class,
        string $property = 'id',
        bool $multiple = false
    ) {
        $this->registry = $registry;
        $this->class    = $class;
        $this->property = $property;
        $this->multiple = $multiple;

        $this->validate();
    }

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function transform($entity)
    {
        if ($entity === null) {
            return null;
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();

        if ($this->isMultiple() && is_iterable($entity)) {
            $value = [];

            foreach ($entity as $e) {
                if (! $accessor->isReadable($entity, $property)) {
                    continue;
                }

                $value[] = $accessor->getValue($e, $property);
            }

            return implode(',', $value);
        }

        if (! $accessor->isReadable($entity, $property)) {
            return null;
        }

        return $accessor->getValue($entity, $property);
    }

    /**
     * @param mixed $id
     *
     * @return mixed
     */
    public function reverseTransform($id)
    {
        if ($id === null) {
            if ($this->isMultiple()) {
                return [];
            }

            return null;
        }

        $repo     = $this->getRepository();
        $property = $this->getProperty();
        $class    = $this->getClass();

        if ($this->isMultiple()) {
            $ids = explode(',', $id);

            return $repo->findBy([$property => $ids]);
        }

        $result = $repo->findOneBy([$property => $id]);

        if ($result === null) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $id));
        }

        return $result;
    }

    protected function getRepository() : ObjectRepository
    {
        return $this->registry->getRepository($this->getClass());
    }

    protected function getClass() : string
    {
        return $this->class;
    }

    protected function getProperty() : string
    {
        return $this->property;
    }

    protected function isMultiple() : bool
    {
        return $this->multiple;
    }

    protected function validate() : void
    {
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo        = new PropertyInfoExtractor([$reflectionExtractor]);

        $properties = $propertyInfo->getProperties($this->class) ?? [];

        if (! in_array($this->property, $properties, true)) {
            throw new NoSuchPropertyException(sprintf('property %s is missing in class %s', $this->property, $this->class));
        }
    }
}
