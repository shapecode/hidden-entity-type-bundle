<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;
use function count;
use function explode;
use function implode;
use function sprintf;

class ObjectsToIdTransformer extends Transformer
{
    /**
     * @param object[]|array|mixed $entity
     *
     * @return string|int|float|null
     */
    public function transform($entity)
    {
        if ($entity === null) {
            return null;
        }

        Assert::isArray($entity);
        Assert::allIsInstanceOf($entity, $this->class);

        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();

        $value = [];

        foreach ($entity as $e) {
            if (! $accessor->isReadable($e, $property)) {
                continue;
            }

            $value[] = $accessor->getValue($e, $property);
        }

        return implode(',', $value);
    }

    /**
     * @param mixed $id
     *
     * @return array|object[]
     */
    public function reverseTransform($id) : array
    {
        if ($id === null) {
            return [];
        }

        $repo     = $this->getRepository();
        $property = $this->getProperty();
        $class    = $this->getClass();

        $ids = explode(',', $id);

        $results = $repo->findBy([$property => $ids]);

        if (count($results) === 0) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $id));
        }

        Assert::isArray($results);
        Assert::allIsInstanceOf($results, $this->class);

        return $results;
    }
}
