<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model;

class TestFormModel
{
    private TestObject|null $object = null;

    public function getObject(): TestObject|null
    {
        return $this->object;
    }

    public function setObject(TestObject|null $object): void
    {
        $this->object = $object;
    }
}
