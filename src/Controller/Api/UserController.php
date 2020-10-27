<?php


namespace App\Controller\Api;


use App\Form\Entity\UserType;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractFOSRestController
{
    use RootFormFactoryTrait;
    use EntityManagerTrait;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Rest\View(serializerGroups={"group1", "group2"})
     * @param Request $request
     * @return View
     */
    public function register(Request $request): View
    {
        $form = $this->createForm(
            UserType::class,
            null,
            [ 'validation_groups' => [ 'password', 'Default' ]]
        );

        $this->submitForm($form, $request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userManager->create($form->getData());
            $this->em->persist($user);
            $this->em->flush();

            return $this
                ->view($user)
                ->setContext((new Context())->setAttribute('jwt', true))
            ;
        }

        return $this->view($form);
    }
}