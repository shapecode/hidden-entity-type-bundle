<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model;

class TestObject
{
    protected string|null $name = null;

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string|null $name): void
    {
        $this->name = $name;
    }
}
