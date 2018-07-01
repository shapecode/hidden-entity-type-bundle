<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

        if ($this->multiple && \is_array($entity)) {
            $value = [];

            foreach ($entity as $e) {
                $value[] = $accessor->getValue($e, $this->property);
            }

            $value = implode(',', $value);
        } else {
            $value = $accessor->getValue($entity, $this->property);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        if ($this->multiple) {
            $ids = explode(',', $id);
            $result = $this->getRepository()->findBy([$this->property => $ids]);
        } else {
            $result = $this->getRepository()->findOneBy([$this->property => $id]);

            if (null === $result) {
                throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $this->class, $this->property, $id));
            }
        }

        return $result;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->registry->getRepository($this->class);
    }
}