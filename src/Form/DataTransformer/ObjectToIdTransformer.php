<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;
use function sprintf;

class ObjectToIdTransformer extends Transformer
{
    /**
     * @param mixed $entity
     *
     * @return string|int|float|null
     */
    public function transform($entity)
    {
        if ($entity === null) {
            return null;
        }

        Assert::isInstanceOf($entity, $this->class);

        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();

        if (! $accessor->isReadable($entity, $property)) {
            return null;
        }

        return $accessor->getValue($entity, $property);
    }

    /**
     * @param mixed $id
     */
    public function reverseTransform($id) : ?object
    {
        if ($id === null) {
            return null;
        }

        $repo     = $this->getRepository();
        $property = $this->getProperty();
        $class    = $this->getClass();

        $result = $repo->findOneBy([
            $property => $id,
        ]);

        if ($result === null) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $id));
        }

        Assert::isInstanceOf($result, $this->class);

        return $result;
    }
}
