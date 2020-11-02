<?php


namespace App\Form\Model;


use App\Entity\Task;
use App\Entity\User;
use App\Form\Transformer\EntityToIdTransformer;
use App\Form\Type\BooleanType;
use App\Model\TimerModel;
use App\Traits\EntityManagerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimerModelType extends AbstractType
{
    use EntityManagerAwareTrait;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * TimerModelType constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $taskTransformer = new EntityToIdTransformer($this->em, Task::class);

        $builder
            ->add('startedAt', DateTimeType::class, [
                'model_timezone' => $user->getTimeZone(),
                'widget' => 'single_text',
            ])
            ->add('hours', NumberType::class)
            ->add('minutes', NumberType::class)
            ->add('force', BooleanType::class)
            ->add('note', TextType::class)
            ->add($builder->create('task', TextType::class)->addModelTransformer($taskTransformer))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimerModel::class,
            'csrf_protection' => false,
            'allow_extra_fields' => false,
        ]);
    }
}