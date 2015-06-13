<?php

namespace Glifery\EntityHiddenTypeBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
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
    protected $em;

    /** @var EntityRepository */
    protected $repository;

    /**
     * @param ManagerRegistry $registry
     * @param string $class
     * @param string $property
     */
    public function __construct(ManagerRegistry $registry, $em, $class, $property)
    {
        $this->class = $class;
        $this->property = $property;
        $this->em = $this->getEntityManager($registry, $em);
        $this->repository = $this->getEntityRepository($this->em, $this->class);
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
            throw new TransformationFailedException(sprintf('Entity must be instance of %s, instance of %s has given.', $className, get_class($entity)));
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
     * @param ObjectManager|string $emName
     * @return ObjectManager
     */
    private function getEntityManager(ManagerRegistry $registry, $emName) {
        if ($emName instanceof ObjectManager) {
            return $emName;
        }

        $emName = (string)$emName;
        if ($em = $registry->getManager($emName)) {
            return $em;
        }

        throw new InvalidConfigurationException(sprintf('Doctrine ORM Manager named "%s" does not exist.', $emName));
    }

    /**
     * @param ObjectManager $em
     * @param string $class
     * @return EntityRepository
     */
    private function getEntityRepository(ObjectManager $em, $class) {
        if ($repo = $em->getRepository($class)) {
            return $repo;
        }

        throw new InvalidConfigurationException(sprintf('Entity Repository for class "%s" does not exist.', $class));
    }
}