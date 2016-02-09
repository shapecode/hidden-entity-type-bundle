<?php

namespace Glifery\EntityHiddenTypeBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ObjectToIdTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $property;

    /**
     * @var EntityManager
     */
    protected $om;

    /** @var EntityRepository */
    protected $repository;

    /**
     * @param ManagerRegistry $registry
     * @param string $class
     * @param string $property
     */
    public function __construct(ManagerRegistry $registry, $om, $class, $property)
    {
        $this->class = $class;
        $this->property = $property;
        $this->em = $this->getObjectManager($registry, $om);
        $this->repository = $this->getObjectRepository($this->em, $this->class);
    }

    /**
     * @param mixed $entity
     * @return mixed|null
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        $className = $this->repository->getClassName();
        if (!$entity instanceof $className) {
            throw new TransformationFailedException(sprintf('Object must be instance of %s, instance of %s has given.', $className, get_class($entity)));
        }

        $methodName = 'get'.ucfirst($this->property);
        if (!method_exists($entity, $methodName)) {
            throw new InvalidConfigurationException(sprintf('There is no getter for property "%s" in class "%s".', $this->property, $this->class));
        }

        return $entity->{$methodName}();
    }

    /**
     * @param mixed $id
     * @return mixed|null|object
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->repository->findOneBy(array($this->property => $id));

        if (null === $entity) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $this->class, $this->property, $id));
        }

        return $entity;
    }

    /**
     * @param ManagerRegistry $registry
     * @param ObjectManager|string $omName
     * @return ObjectManager
     */
    private function getObjectManager(ManagerRegistry $registry, $omName) {
        if ($omName instanceof ObjectManager) {
            return $omName;
        }

        $omName = (string)$omName;
        if ($om = $registry->getManager($omName)) {
            return $om;
        }

        throw new InvalidConfigurationException(sprintf('Doctrine Manager named "%s" does not exist.', $omName));
    }

    /**
     * @param ObjectManager $om
     * @param string $class
     * @return ObjectRepository
     */
    private function getObjectRepository(ObjectManager $om, $class) {
        if ($repo = $om->getRepository($class)) {
            return $repo;
        }

        throw new InvalidConfigurationException(sprintf('Repository for class "%s" does not exist.', $class));
    }
}