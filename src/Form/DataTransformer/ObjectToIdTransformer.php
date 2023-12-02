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

/** @template-extends Transformer<object, string> */
class ObjectToIdTransformer extends Transformer
{
    public function transform(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        Assert::isInstanceOf($value, $this->class);

        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();

        if (! $accessor->isReadable($value, $property)) {
            return null;
        }

        $valueObject = $accessor->getValue($value, $property);

        if ($valueObject === null) {
            return null;
        }

        if (! is_string($valueObject) && ! is_numeric($valueObject)) {
            throw new LogicException('id hast to be string or integer', 1653564596059);
        }

        return (string) $valueObject;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $repo     = $this->getRepository();
        $property = $this->getProperty();
        $class    = $this->getClass();

        $result = $repo->findOneBy([
            $property => $value,
        ]);

        if ($result === null) {
            throw new TransformationFailedException(
                sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $value),
                1701526691297,
            );
        }

        Assert::isInstanceOf($result, $this->class);

        return $result;
    }
}
