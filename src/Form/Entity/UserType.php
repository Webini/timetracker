<?php


namespace App\Form\Entity;


use App\Entity\User;
use App\Form\Type\BooleanType;
use App\Security\Voter\UserVoter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('plainPassword', TextType::class, [ 'constraints' => new NotBlank() ])
            ->add('phoneNumber', TelType::class)
            ->add('email', EmailType::class, [ 'constraints' => new NotBlank() ])
        ;

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        if ($this->authorizationChecker->isGranted(UserVoter::USER_CREATE_PROJECT_MANAGER)) {
            $builder->add('projectManager', BooleanType::class);
        }
        if ($this->authorizationChecker->isGranted(UserVoter::USER_CREATE_ADMIN)) {
            $builder->add('admin', BooleanType::class);
        }
        if ($this->authorizationChecker->isGranted(UserVoter::USER_CREATE_SUPER_ADMIN)) {
            $builder->add('superAdmin', BooleanType::class);
        }
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
        ]);
    }

}