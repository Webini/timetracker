<?php


namespace App\Form\Entity;


use App\Entity\AssignedUser;
use App\Entity\User;
use App\Form\Transformer\EntityToIdTransformer;
use App\Traits\AuthorizationCheckerAwareTrait;
use App\Traits\EntityManagerAwareTrait;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class AssignedUserType extends AbstractType
{
    use AuthorizationCheckerAwareTrait;
    use EntityManagerAwareTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN]);

        if ($options['creation']) {
            $userTransformer = new EntityToIdTransformer($this->em, User::class);
            $builder->add(
                $builder
                    ->create('assigned', TextType::class, [
                        'required' => true,
                        'constraints' => new NotBlank()
                    ])
                    ->addModelTransformer($userTransformer)
            );
        }

        $builder
            ->add('permissions', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(AssignedUser::PERMISSION_NONE),
                    new LessThanOrEqual($admin ? AssignedUser::PERMISSIONS_ALL : AssignedUser::PERMISSIONS_TASK_CUD)
                ]
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssignedUser::class,
            'csrf_protection' => false,
            'allow_extra_fields' => false,
            'creation' => false,
        ]);

        $resolver->setAllowedTypes('creation', [ 'boolean', 'null' ]);
    }
}