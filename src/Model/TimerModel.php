<?php


namespace App\Model;


use App\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;


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
     * @Assert\Expression(
     *     "this.getStartedAt() !== '' &&
               (this.getMinutes() === null || this.getMinutes() === 0 || this.getMinutes() === '') &&
               (value === null || value === '' || value === 0)",
     *     message="This field is missing."
     * )
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(23)
     */
    private $hours;

    /**
     * @var integer|null
     * @Assert\Expression(
     *     "this.getStartedAt() !== '' &&
               (this.getHours() === null || this.getHours() === 0 || this.getHours() === '') &&
               (value === null || value === '' || value === 0)",
     *     message="This field is missing."
     * )
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
}