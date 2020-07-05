<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HiddenDocumentType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'The document does not exist.',
        ]);
    }

    public function getParent(): string
    {
        return HiddenObjectType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'shapecode_hidden_document';
    }
}
