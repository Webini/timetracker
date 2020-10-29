<?php


namespace App\Controller\Api;


use App\Entity\User;
use App\Form\Entity\UserType;
use App\Form\Model\UserSearchType;
use App\Manager\UserManager;
use App\Security\Voter\UserVoter;
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
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $form = $this->createForm(
            UserType::class,
            null,
            [
                'with_password' => true,
                'validation_groups' => [ 'password', 'Default' ]
            ]
        );

        $this->submitForm($form, $request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userManager->create($form->getData());
            $this->em->persist($user);
            $this->em->flush();

            return $this
                ->view($user)
                ->setContext(
                    (new Context())
                        ->setAttribute('jwt', true)
                        ->setGroups([ 'user_full' ])
                )
            ;
        }

        return $this->view($form);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return View
     */
    public function update(Request $request, User $user): View
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_EDIT, $user);
        $form = $this->createForm(UserType::class, $user);

        $this->submitRequestContent($form, $request, false);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this
                ->view($user)
                ->setContext((new Context())->setGroups([ 'user_full' ]))
            ;
        }

        return $this->view($form);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function createLogged(Request $request): View
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_CREATE);
        return $this->create($request);
    }

    /**
     * @param User $user
     * @return View
     */
    public function getOne(User $user): View
    {
        return $this->view($user);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function search(Request $request): View
    {
        $form = $this->createForm(UserSearchType::class, null, [ 'method' => 'GET' ]);
        $this->submitRequestQuery($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->em->getRepository(User::class);
            return $this->view($repo->search($form->getData()));
        }

        return $this->view($form);
    }
}