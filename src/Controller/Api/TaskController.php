<?php


namespace App\Controller\Api;


use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Entity\Task;
use App\Form\Entity\TaskType;
use App\Manager\AssignedUserManager;
use App\Manager\TaskManager;
use App\Security\Voter\TaskVoter;
use App\Traits\EntityManagerAwareTrait;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractFOSRestController
{
    use SubmitFormTrait;
    use EntityManagerAwareTrait;

    /**
     * @var TaskManager
     */
    private $taskManager;

    /**
     * @var AssignedUserManager
     */
    private $assignedUserManager;

    /**
     * TaskController constructor.
     * @param TaskManager $taskManager
     */
    public function __construct(TaskManager $taskManager, AssignedUserManager $assignedUserManager)
    {
        $this->assignedUserManager = $assignedUserManager;
        $this->taskManager = $taskManager;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return View
     */
    public function create(Request $request, Project $project): View
    {
        $assignedUser = $this->assignedUserManager->getAssignedUserFor($project, $this->getUser());
        $this->denyAccessUnlessGranted(TaskVoter::TASK_CREATE, $assignedUser);

        $form = $this->createForm(TaskType::class, null, [ 'creation' => true ]);
        $this->submitRequestContent($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $this->taskManager->createFor($project, $form->getData());
            $this->em->persist($task);
            $this->em->flush();

            return $this->view($task);
        }

        return $this->view($form);
    }

    /**
     * @Entity("task", expr="repository.findOneForProject(project, task)")
     * @param Request $request
     * @param Task $task
     * @return View
     */
    public function update(Request $request, Task $task): View
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_UPDATE, $task);

        $form = $this->createForm(TaskType::class, $task);
        $this->submitRequestContent($form, $request, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->view($task);
        }

        return $this->view($form);
    }
}