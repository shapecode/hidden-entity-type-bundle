<?php

namespace Glifery\EntityHiddenTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $property;

    /**
     * @param ObjectManager $objectManager
     * @param string $class
     * @param string $property
     */
    public function __construct(ObjectManager $objectManager, $class, $property)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
        $this->property = $property;
    }

    /**
     * @param mixed $entity
     * @return mixed
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return;
        }

        $methodName = 'get'.ucfirst($this->property);

        return $entity->{$methodName}();
    }

    /**
     * @param integer $id
     * @return mixed|null|object
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->objectManager
            ->getRepository($this->class)
            ->findOneBy(array($this->property => $id));

        if (null === $entity) {
            throw new TransformationFailedException();
        }

        return $entity;
    }
}