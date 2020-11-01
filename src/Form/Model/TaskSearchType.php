<?php


namespace App\Form\Model;


use App\Model\TaskSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', IntegerType::class, [ 'required' => false ])
            ->add('limit', IntegerType::class, [ 'required' => false ])
            ->add('search', TextType::class, [ 'required' => false ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskSearch::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}