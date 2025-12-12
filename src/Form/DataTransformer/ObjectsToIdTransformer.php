<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function count;
use function explode;
use function implode;
use function sprintf;

/** @template-extends Transformer<array<array-key, object>, string> */
final readonly class ObjectsToIdTransformer extends Transformer
{
    public function transform(mixed $value): string|null
    {
        if ($value === null) {
            return null;
        }

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

    /** @return array<array-key, object> */
    public function reverseTransform(mixed $value): array
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

        return $results;
    }
}
