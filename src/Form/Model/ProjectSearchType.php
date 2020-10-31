<?php


namespace App\Form\Model;


use App\Model\ProjectSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', IntegerType::class, [ 'required' => false ])
            ->add('limit', IntegerType::class, [ 'required' => false ])
            ->add('search', TextType::class, [ 'required' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectSearch::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}