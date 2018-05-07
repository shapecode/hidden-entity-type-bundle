<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HiddenEntityType
 *
 * @package Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type
 * @author  Nikita Loges
 */
class HiddenEntityType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The entity does not exist.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return HiddenObjectType::class;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'shapecode_hidden_entity';
    }
}