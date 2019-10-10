<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model;

class TestFormModel
{
    /** @var TestObject|null */
    private $object;

    public function getObject() : ?TestObject
    {
        return $this->object;
    }

    public function setObject(?TestObject $object) : void
    {
        $this->object = $object;
    }
}
