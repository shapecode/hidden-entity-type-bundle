<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model;

class TestFormModel
{
    private ?TestObject $object = null;

    public function getObject(): ?TestObject
    {
        return $this->object;
    }

    public function setObject(?TestObject $object): void
    {
        $this->object = $object;
    }
}
