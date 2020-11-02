<?php


namespace App\Model;


use App\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class TimerModel
{
    /**
     * @Assert\LessThan("now")
     * @var \DateTime|null
     */
    protected $startedAt;

    /**
     * @var integer|null
     *
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(23)
     */
    private $hours;

    /**
     * @var integer|null
     *
     * @Assert\Callback({ TimerModel::class, "validateTime" })
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(59)
     */
    private $minutes;

    /**
     * @var string|null
     */
    private $note;

    /**
     * @Assert\NotBlank()
     * @var Task|null
     */
    private $task;

    /**
     * @var bool|null
     */
    private $force;

    /**
     * @return \DateTime|null
     */
    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTime|null $startedAt
     * @return $this
     */
    public function setStartedAt(?\DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHours(): ?int
    {
        return $this->hours;
    }

    /**
     * @param int|null $hours
     * @return $this
     */
    public function setHours(?int $hours): self
    {
        $this->hours = $hours;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinutes(): ?int
    {
        return $this->minutes;
    }

    /**
     * @param int|null $minutes
     * @return $this
     */
    public function setMinutes(?int $minutes): self
    {
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     * @return $this
     */
    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return Task|null
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     * @return $this
     */
    public function setTask(?Task $task): self
    {
        $this->task = $task;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getForce(): ?bool
    {
        return $this->force;
    }

    /**
     * @param bool|null $force
     * @return $this
     */
    public function setForce(?bool $force): self
    {
        $this->force = $force;
        return $this;
    }

    /**
     * @param string|null $value
     * @param ExecutionContextInterface $context
     */
    public static function validateTime(?string $value, ExecutionContextInterface $context)
    {
        $object = $context->getObject();
        if ($object->getStartedAt() === null) {
            return;
        }

        if (!empty($object->getHours()) || !empty($object->getMinutes())) {
            return;
        }

        $context->addViolation("You must set at least one hour or one minute");
    }
}