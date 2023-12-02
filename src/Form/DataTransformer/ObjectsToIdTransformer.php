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

/** @template-extends Transformer<object[], string> */
class ObjectsToIdTransformer extends Transformer
{
    public function transform(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        Assert::allIsInstanceOf($value, $this->class);

        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();

        $valueIds = [];

        foreach ($value as $e) {
            if (! $accessor->isReadable($e, $property)) {
                continue;
            }

            $valueIds[] = $accessor->getValue($e, $property);
        }

        return implode(',', $valueIds);
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ($value === null) {
            return [];
        }

        $repo     = $this->getRepository();
        $property = $this->getProperty();
        $class    = $this->getClass();

        $ids = explode(',', $value);

        $results = $repo->findBy([$property => $ids]);

        if (count($results) === 0) {
            throw new TransformationFailedException(
                sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $value),
                1701526676576,
            );
        }

        Assert::allIsInstanceOf($results, $this->class);

        return $results;
    }
}
