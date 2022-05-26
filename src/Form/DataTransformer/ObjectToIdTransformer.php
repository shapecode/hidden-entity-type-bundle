<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use LogicException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

use function is_numeric;
use function is_string;
use function sprintf;

/**
 * @template-extends Transformer<object, string>
 */
class ObjectToIdTransformer extends Transformer
{
    /**
     * @phpstan-param object|null $entity
     *
     * @phpstan-return string|null
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

        $value = $accessor->getValue($entity, $property);

        if ($value === null) {
            return null;
        }

        if (! is_string($value) && ! is_numeric($value)) {
            throw new LogicException('id hast to be string or integer', 1653564596059);
        }

        return (string) $value;
    }

    /**
     * @phpstan-param string|null $id
     *
     * @phpstan-return object|null
     */
    public function reverseTransform($id): ?object
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
