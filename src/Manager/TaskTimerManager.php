<?php


namespace App\Manager;


use App\Entity\TaskTimer;
use App\Entity\User;
use App\Exception\RunningTimerException;
use App\Model\TimerModel;
use App\Traits\EntityManagerAwareTrait;
use Doctrine\ORM\EntityManagerInterface;

class TaskTimerManager
{

    /**
     * @var \App\Repository\TaskTimerRepository
     */
    private $repo;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TimeZoneManager
     */
    private $timeZoneManager;

    /**
     * TaskTimerManager constructor.
     * @param EntityManagerInterface $em
     * @param TimeZoneManager $timeZoneManager
     */
    public function __construct(EntityManagerInterface $em, TimeZoneManager $timeZoneManager)
    {
        $this->timeZoneManager = $timeZoneManager;
        $this->em = $em;
        $this->repo = $em->getRepository(TaskTimer::class);
    }

    /**
     * @param User $user
     * @return TaskTimer|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRunningTimer(User $user): ?TaskTimer
    {
        return $this->repo->findRunningTimerFor($user);
    }

    /**
     * @param TaskTimer $timer
     * @return $this
     */
    public function stop(TaskTimer $timer, ?User $currentUser): self
    {
        $timer->setStoppedAt($this->timeZoneManager->createDate('now', $currentUser));
        return $this;
    }

    /**
     * @param User $user
     * @param TimerModel $timer
     * @param User|null $createdBy
     * @return TaskTimer
     * @throws \Exception
     */
    public function createFor(User $user, TimerModel $timer, ?User $createdBy = null): TaskTimer
    {
        $taskTimer = new TaskTimer();
        $taskTimer
            ->setOwner($user)
            ->setNote($timer->getNote())
            ->setTask($timer->getTask())
            ->setCreatedBy($createdBy)
        ;

        $startedAt = $timer->getStartedAt();
        if ($startedAt !== null) {
            $stoppedAt = clone $startedAt;
            if ($timer->getHours()) {
                $stoppedAt->modify('+' . $timer->getHours() . 'hours');
            }
            if ($timer->getMinutes()) {
                $stoppedAt->modify('+' . $timer->getMinutes() . 'minutes');
            }

            $taskTimer
                ->setStartedAt($startedAt)
                ->setStoppedAt($stoppedAt)
            ;
        } else {
            $taskTimer->setStartedAt(
                $this->timeZoneManager->createDate('now', $createdBy)
            );
        }

        return $taskTimer;
    }
}