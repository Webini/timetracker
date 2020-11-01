<?php


namespace App\Controller\Api\Project;


use App\Controller\Api\SubmitFormTrait;
use App\Entity\Project;
use App\Form\Entity\ProjectType;
use App\Form\Model\ProjectSearchType;
use App\Manager\ProjectManager;
use App\Security\Voter\ProjectVoter;
use App\Traits\EntityManagerAwareTrait;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractFOSRestController
{
    use EntityManagerAwareTrait;
    use SubmitFormTrait;

    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * ProjectController constructor.
     * @param ProjectManager $projectManager
     */
    public function __construct(ProjectManager $projectManager)
    {
        $this->projectManager = $projectManager;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $this->denyAccessUnlessGranted(ProjectVoter::PROJECT_CREATE);

        $form = $this->createForm(ProjectType::class);
        $this->submitRequestContent($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $this->projectManager->create($form->getData());

            $this->em->persist($project);
            $this->em->flush();

            return $this->view($project);
        }

        return $this->view($form);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return View
     */
    public function update(Request $request, Project $project): View
    {
        $this->denyAccessUnlessGranted(ProjectVoter::PROJECT_UPDATE, $project);
        $form = $this->createForm(ProjectType::class, $project);

        $this->submitRequestContent($form, $request, false);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->view($project);
        }

        return $this->view($form);
    }

    /**
     * @param Project $project
     * @return View
     */
    public function getOne(Project $project): View
    {
        $this->denyAccessUnlessGranted(ProjectVoter::PROJECT_READ, $project);
        return $this->view($project);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function search(Request $request): View
    {
        $form = $this->createForm(ProjectSearchType::class);
        $this->submitRequestQuery($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->em->getRepository(Project::class);
            return $this->view($repo->searchMyProjectsPaginated(
                $this->getUser(),
                $form->getData()
            ));
        }

        return $this->view($form);
    }
}