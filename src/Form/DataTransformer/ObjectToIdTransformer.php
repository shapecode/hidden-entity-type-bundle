<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use function explode;
use function implode;
use function is_iterable;

/**
 * Class ObjectToIdTransformer
 *
 * @package Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer
 * @author  Nikita Loges
 */
class ObjectToIdTransformer implements DataTransformerInterface
{

    /** @var ManagerRegistry */
    protected $registry;

    /** @var string */
    protected $class;

    /** @var string */
    protected $property;

    /** @var boolean */
    protected $multiple = false;

    /**
     * @param ManagerRegistry $registry
     * @param string          $class
     * @param string          $property
     * @param bool            $multiple
     */
    public function __construct(ManagerRegistry $registry, string $class, string $property = 'id', bool $multiple = false)
    {
        $this->registry = $registry;
        $this->class = $class;
        $this->property = $property;
        $this->multiple = $multiple;
    }

    /**
     * @inheritdoc
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();

        if ($this->isMultiple() && is_iterable($entity)) {
            $value = [];

            foreach ($entity as $e) {
                if ($accessor->isReadable($entity, $property)) {
                    $value[] = $accessor->getValue($e, $property);
                }
            }

            return implode(',', $value);
        }

        if (!$accessor->isReadable($entity, $property)) {
            return null;
        }

        return $accessor->getValue($entity, $property);
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $repo = $this->getRepository();
        $property = $this->getProperty();
        $class = $this->getClass();

        if ($this->isMultiple()) {
            $ids = explode(',', $id);

            return $repo->findBy([$property => $ids]);
        }

        $result = $repo->findOneBy([$property => $id]);

        if (null === $result) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $id));
        }

        return $result;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->registry->getRepository($this->getClass());
    }

    /**
     * @return string
     */
    protected function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    protected function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return bool
     */
    protected function isMultiple(): bool
    {
        return $this->multiple;
    }
}
