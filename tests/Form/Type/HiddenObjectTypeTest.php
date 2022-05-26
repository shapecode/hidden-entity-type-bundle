<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenObjectType;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestFormModel;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestObject;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

use function array_keys;
use function assert;

/**
 * @covers \Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenObjectType
 */
class HiddenObjectTypeTest extends TypeTestCase
{
    private ManagerRegistry $registry;

    private TestObject $testObject;

    protected function setUp(): void
    {
        $object = new TestObject();
        $object->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => $object,
            'findBy'    => [$object],
        ]);

        $this->registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $this->testObject = $object;

        parent::setUp();
    }

    /**
     * @return list<FormExtensionInterface>
     */
    protected function getExtensions(): array
    {
        // create a type instance with the mocked dependencies
        $type = new HiddenObjectType($this->registry);

        return [
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([$type], []),
        ];
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'object' => 'test',
        ];

        $data = new TestFormModel();

        $form = $this->factory->create(TestFormType::class, $data);
        $form->submit($formData);

        $testObject = $data->getObject();
        assert($testObject instanceof TestObject);

        self::assertTrue($form->isSynchronized());
        self::assertEquals($this->testObject->getName(), $testObject->getName());

        $view     = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }
}
