<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HiddenDocumentType
 *
 * @package Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type
 * @author  Nikita Loges
 */
class HiddenDocumentType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'The document does not exist.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getParent(): string
    {
        return HiddenObjectType::class;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'shapecode_hidden_document';
    }
}