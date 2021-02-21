<?php


namespace App\Controller\Api\Project;


use App\Controller\Api\SubmitFormTrait;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\TaskTimer;
use App\Entity\User;
use App\Form\Entity\TaskType;
use App\Form\Model\TaskSearchType;
use App\Manager\AssignedUserManager;
use App\Manager\TaskManager;
use App\Security\Voter\ProjectVoter;
use App\Security\Voter\TaskVoter;
use App\Traits\EntityManagerAwareTrait;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param AssignedUserManager $assignedUserManager
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

            return $this->generateView($task, $task);
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
            return $this->generateView($task, $task);
        }

        return $this->view($form);
    }

    /**
     * @Entity("task", expr="repository.findOneForProject(project, task)")
     * @param Task $task
     * @return View
     */
    public function getOne(Task $task): View
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_READ, $task);
        return $this->generateView($task, $task);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return View
     */
    public function search(Request $request, Project $project): View
    {
        $this->denyAccessUnlessGranted(ProjectVoter::PROJECT_READ, $project);

        $form = $this->createForm(TaskSearchType::class);
        $this->submitRequestQuery($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tasksRepo = $this->em->getRepository(Task::class);
            $results = $tasksRepo->searchPaginated($project, $form->getData());
            return $this->generateView($results, $results->getItems());
        }

        return $this->view($form);
    }

    /**
     * @Entity("task", expr="repository.findOneForProject(project, task)")
     * @param Task $task
     * @return View
     */
    public function delete(Task $task): View
    {
        $this->denyAccessUnlessGranted(TaskVoter::TASK_DELETE, $task);

        $this->taskManager->delete($task);
        $this->em->remove($task);
        $this->em->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $output
     * @param Tasks[]|\Traversable $tasks
     * @return View
     */
    private function generateView($output, $tasks): View
    {
        /** @var User $user */
        $user = $this->getUser();
        $timersRepo = $this->em->getRepository(TaskTimer::class);
        $timers = $timersRepo->findTimeSpent(
            $tasks,
            $user->hasOneRole(User::ROLE_PROJECT_MANAGER | User::ROLE_ADMIN | User::ROLE_SUPER_ADMIN)
                ? NULL
                : $user
        );

        return $this
            ->view($output)
            ->setContext((new Context())->setAttribute('timers', $timers))
        ;
    }
}
