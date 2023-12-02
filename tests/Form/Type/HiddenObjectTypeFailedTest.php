<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenObjectType;
use Shapecode\Bundle\HiddenEntityTypeBundle\Tests\Model\TestFormModel;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

use function array_keys;

#[CoversClass(HiddenObjectType::class)]
class HiddenObjectTypeFailedTest extends TypeTestCase
{
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => null,
            'findBy'    => [],
        ]);

        $this->registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        parent::setUp();
    }

    /** @return list<FormExtensionInterface> */
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

        self::assertTrue($form->isSynchronized());
        self::assertEquals(null, $data->getObject());

        $view     = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }
}
