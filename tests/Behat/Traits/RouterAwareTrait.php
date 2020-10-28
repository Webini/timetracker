<?php


namespace App\Tests\Behat\Traits;


use Symfony\Component\Routing\RouterInterface;

trait RouterAwareTrait
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @required
     * @param RouterInterface $router
     * @return $this
     */
    public function setRouter(RouterInterface $router): self
    {
        $this->router = $router;
        return $this;
    }
}