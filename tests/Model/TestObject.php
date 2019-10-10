<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model;

class TestObject
{
    /** @var string|null */
    protected $name;

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
}
