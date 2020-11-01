<?php


namespace App\Form\Entity;


use App\Entity\Task;
use App\Form\Type\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
        ;

        if (!$options['creation']) {
            $builder->add('archived', BooleanType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'csrf_protection' => false,
            'allow_extra_fields' => false,
            'creation' => false,
        ]);

        $resolver->setAllowedTypes('creation', [ 'boolean', 'null' ]);
    }
}