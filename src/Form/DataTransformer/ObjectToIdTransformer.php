<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
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

    /** @var string */
    protected $class;

    /** @var string */
    protected $property;

    /** @var EntityManager */
    protected $em;

    /** @var EntityRepository */
    protected $repository;

    /**
     * @param ManagerRegistry $registry
     * @param string          $class
     * @param string          $property
     */
    public function __construct(ManagerRegistry $registry, $class, $property)
    {
        $this->class = $class;
        $this->property = $property;
        $this->em = $this->getObjectManager($registry, $this->class);
        $this->repository = $this->getObjectRepository($this->em, $this->class);
    }

    /**
     * @param mixed $entity
     *
     * @return mixed|null
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        $value = $accessor->getValue($entity, $this->property);

        return $value;
    }

    /**
     * @param mixed $id
     *
     * @return mixed|null|object
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->repository->findOneBy([$this->property => $id]);

        if (null === $entity) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $this->class, $this->property, $id));
        }

        return $entity;
    }

    /**
     * @param ManagerRegistry $registry
     * @param string          $class
     *
     * @return ObjectManager
     */
    private function getObjectManager(ManagerRegistry $registry, $class)
    {
        if ($manager = $registry->getManagerForClass($class)) {
            return $manager;
        }

        throw new InvalidConfigurationException(sprintf('Doctrine Manager for class "%s" does not exist.', $class));
    }

    /**
     * @param ObjectManager $manager
     * @param string        $class
     *
     * @return ObjectRepository
     */
    private function getObjectRepository(ObjectManager $manager, $class)
    {
        if ($repo = $manager->getRepository($class)) {
            return $repo;
        }

        throw new InvalidConfigurationException(sprintf('Repository for class "%s" does not exist.', $class));
    }
}