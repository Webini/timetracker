<?php


namespace App\Controller\Api\User;


use App\Controller\Api\SubmitFormTrait;
use App\Entity\User;
use App\Form\Model\TimerModelType;
use App\Manager\TaskTimerManager;
use App\Model\TimerModel;
use App\Security\Voter\TaskTimerVoter;
use App\Traits\EntityManagerAwareTrait;
use FOS\RestBundle\Context\Context;
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
        $manager = $this->taskTimerManager;
        $taskTimer = $manager->createFor($user, $timer);
        $this->denyAccessUnlessGranted(TaskTimerVoter::TIMER_CREATE, $taskTimer);

        $runningTimer = $manager->getRunningTimer($user);
        if ($runningTimer !== null) {
            if ($timer->getForce()) {
                $manager->stop($runningTimer);
            } else {
                return $this->view(null, Response::HTTP_CONFLICT);
            }
        }

        $this->em->persist($taskTimer);
        $this->em->flush();

        return $this
            ->view($taskTimer)
            ->setContext((new Context())->setAttribute('withTask', true))
        ;
    }

    /**
     * @param User $user
     * @return View
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function stop(User $user): View
    {
        $manager = $this->taskTimerManager;
        $runningTimer = $manager->getRunningTimer($user);
        if ($runningTimer === null) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(TaskTimerVoter::TIMER_STOP, $runningTimer);

        $manager->stop($runningTimer);
        $this->em->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param User $user
     * @return View
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOne(User $user): View
    {
        $manager = $this->taskTimerManager;
        $runningTimer = $manager->getRunningTimer($user);
        if ($runningTimer === null) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(TaskTimerVoter::TIMER_READ_RUNNING, $runningTimer);
        return $this
            ->view($runningTimer)
            ->setContext((new Context())->setAttribute('withTask', true))
        ;
    }
}
