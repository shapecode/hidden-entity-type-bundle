<?php

namespace Glifery\EntityHiddenTypeBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Glifery\EntityHiddenTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentHiddenType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
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
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ObjectToIdTransformer($this->registry, $options['dm'], $options['class'], $options['property']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array('class'))
            ->setDefaults(array(
                    'data_class' => null,
                    'invalid_message' => 'The document does not exist.',
                    'property' => 'id',
                    'dm' => 'default'
                ))
            ->setAllowedTypes(array(
                    'invalid_message' => array('null', 'string'),
                    'property' => array('null', 'string'),
                    'dm' => array('null', 'string', 'Doctrine\Common\Persistence\ObjectManager'),
                ))
        ;
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'document_hidden';
    }
}