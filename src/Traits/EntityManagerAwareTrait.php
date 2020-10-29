<?php


namespace App\Traits;


use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAwareTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @required
     * @param EntityManagerInterface $em
     * @return $this
     */
    public function setEntityManager(EntityManagerInterface $em): self
    {
        $this->em = $em;
        return $this;
    }
}