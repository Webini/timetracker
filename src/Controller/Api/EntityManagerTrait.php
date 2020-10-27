<?php


namespace App\Controller\Api;


use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerTrait
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