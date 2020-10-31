<?php


namespace App\Form\Entity;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('phoneNumber', TelType::class)
            ->add('email', EmailType::class, [ 'constraints' => new NotBlank() ])
        ;

        if ($options['with_password']) {
            $builder->add('plainPassword', TextType::class, [ 'constraints' => new NotBlank() ]);
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $isAdmin = $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN]);
        $isSAdmin = $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_SUPER_ADMIN]);

        if (!$isAdmin && !$isSAdmin) {
            return;
        }

        $roles = [
            User::ROLES[User::ROLE_USER],
            User::ROLES[User::ROLE_PROJECT_MANAGER]
        ];

        if ($isSAdmin) {
            $roles[] = User::ROLES[User::ROLE_ADMIN];
            $roles[] = User::ROLES[User::ROLE_SUPER_ADMIN];
        }

        $builder->add('roles', ChoiceType::class, [
            'choices' => $roles,
            'choice_value' => function ($data) {
                if (is_array($data)) {
                    return $data[0];
                }
                return $data;
            },
            'expanded' => false,
            'multiple' => false
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
            'allow_extra_fields' => false,
            'with_password' => false
        ]);

        $resolver->addAllowedTypes('with_password', [ 'boolean', 'null' ]);
    }

}