<?php

namespace App\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait TimestampableEntity
{
    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetimetz")
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetimetz")
     */
    protected $updatedAt;

    /**
     * Sets createdAt.
     *
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
