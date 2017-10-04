<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HiddenDocumentType
 *
 * @package Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type
 * @author  Nikita Loges
 */
class HiddenDocumentType extends AbstractType
{

    /** @var ManagerRegistry */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ObjectToIdTransformer($this->registry, $options['class'], $options['property']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['class']);

        $resolver->setDefaults([
            'data_class'      => null,
            'invalid_message' => 'The document does not exist.',
            'property'        => 'id'
        ]);

        $resolver->setAllowedTypes('invalid_message', ['null', 'string']);
        $resolver->setAllowedTypes('property', ['null', 'string']);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'shapecode_hidden_document';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}