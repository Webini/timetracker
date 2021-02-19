<?php


namespace App\Form\Model;


use App\Entity\Project;
use App\Form\Transformer\EntityToIdTransformer;
use App\Model\UserSearch;
use App\Traits\EntityManagerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSearchType extends AbstractType
{
    use EntityManagerAwareTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projectTransformer = new EntityToIdTransformer($this->em, Project::class);

        $builder
            ->add('page', IntegerType::class)
            ->add('limit', IntegerType::class)
            ->add('search', TextType::class)
            ->add($builder
                ->create('notInProject', TextType::class)
                ->addModelTransformer($projectTransformer)
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSearch::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
