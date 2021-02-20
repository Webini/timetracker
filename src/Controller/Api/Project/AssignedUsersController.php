<?php


namespace App\Controller\Api\Project;


use App\Controller\Api\SubmitFormTrait;
use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Form\Entity\AssignedUserType;
use App\Manager\AssignedUserManager;
use App\Security\Voter\AssignedUserVoter;
use App\Traits\EntityManagerAwareTrait;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use PhpParser\Node\Expr\Assign;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AssignedUsersController extends AbstractFOSRestController
{
    use EntityManagerAwareTrait;
    use SubmitFormTrait;

    /**
     * @var AssignedUserManager
     */
    private $assignedUserManager;

    /**
     * AssignedUsersController constructor.
     * @param AssignedUserManager $assignedUserManager
     */
    public function __construct(AssignedUserManager $assignedUserManager)
    {
        $this->assignedUserManager = $assignedUserManager;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return View
     */
    public function create(Request $request, Project $project): View
    {
        $this->denyAccessUnlessGranted(AssignedUserVoter::ASSIGNED_USER_CREATE, $project);

        $assignedUser = new AssignedUser();
        $assignedUser->setProject($project);
        $form = $this->createForm(AssignedUserType::class, $assignedUser, [ 'creation' => true ]);
        $this->submitRequestContent($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assignedUser = $this->assignedUserManager->create(
                $project,
                $assignedUser->getAssigned(),
                $assignedUser
            );

            $this->em->persist($assignedUser);
            $this->em->flush();

            return $this->view(null, Response::HTTP_CREATED);
        }

        return $this->view($form);
    }

    /**
     * @Entity("assignedUser", expr="repository.findForProjectAndUser(project, user)")
     * @param Request $request
     * @param Project $project
     * @param AssignedUser $assignedUser
     * @return View
     */
    public function update(Request $request, Project $project, AssignedUser $assignedUser): View
    {
        $this->denyAccessUnlessGranted(AssignedUserVoter::ASSIGNED_USER_UPDATE, $project);

        $form = $this->createForm(AssignedUserType::class, $assignedUser);
        $this->submitRequestContent($form, $request, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->view($assignedUser);
        }

        return $this->view($form);
    }

    /**
     * @param Project $project
     * @return View
     */
    public function getAll(Project $project): View
    {
        $this->denyAccessUnlessGranted(AssignedUserVoter::ASSIGNED_USER_READ_ALL, $project);
        $repo = $this->em->getRepository(AssignedUser::class);
        return $this->view($repo->findAllForProject($project));
    }

    /**
     * @Entity("assignedUser", expr="repository.findForProjectAndUser(project, user)")
     * @param Project $project
     * @param AssignedUser $assignedUser
     * @return View
     */
    public function delete(Project $project, AssignedUser $assignedUser): View
    {
        $this->denyAccessUnlessGranted(AssignedUserVoter::ASSIGNED_USER_DELETE, $project);
        $this->assignedUserManager->delete($assignedUser);
        $this->em->remove($assignedUser);
        $this->em->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Entity("assignedUser", expr="repository.findForProjectAndUser(project, user)")
     * @param Project $project
     * @param AssignedUser $assignedUser
     * @return View
     */
    public function getOne(Project $project, AssignedUser $assignedUser): View
    {
        $this->denyAccessUnlessGranted(AssignedUserVoter::ASSIGNED_USER_READ, $assignedUser);
        return $this->view($assignedUser);
    }
}
