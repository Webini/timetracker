<?php


namespace App\Controller\Api\User;


use App\Controller\Api\SubmitFormTrait;
use App\Entity\User;
use App\Form\Model\TimerModelType;
use App\Manager\TaskTimerManager;
use App\Model\TimerModel;
use App\Security\Voter\TaskTimerVoter;
use App\Traits\EntityManagerAwareTrait;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TimerController extends AbstractFOSRestController
{
    use SubmitFormTrait;
    use EntityManagerAwareTrait;

    /**
     * @var TaskTimerManager
     */
    private $taskTimerManager;

    /**
     * TimerController constructor.
     * @param TaskTimerManager $taskTimerManager
     */
    public function __construct(TaskTimerManager $taskTimerManager)
    {
        $this->taskTimerManager = $taskTimerManager;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return View
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function start(Request $request, User $user): View
    {
        $form = $this->createForm(TimerModelType::class);
        $this->submitRequestContent($form, $request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->view($form);
        }

        /** @var TimerModel $timer */
        $timer = $form->getData();
        $taskTimer = $this->taskTimerManager->createFor($user, $timer);
        $this->denyAccessUnlessGranted(TaskTimerVoter::TIMER_CREATE, $taskTimer);

        $runningTimer = $this->taskTimerManager->getRunningTimer($user);
        if ($runningTimer !== null) {
            if ($timer->getForce()) {
                $this->taskTimerManager->stopTimer($runningTimer);
            } else {
                return $this->view(null, Response::HTTP_CONFLICT);
            }
        }

        $this->em->persist($taskTimer);
        $this->em->flush();

        return $this->view($taskTimer);
    }

}