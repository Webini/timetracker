<?php


namespace App\Controller\Api;

use App\Manager\TimeZoneManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;

class TimeZoneController extends AbstractFOSRestController
{
    /**
     * @var TimeZoneManager
     */
    private $timeZoneManager;

    /**
     * TimeZoneController constructor.
     * @param TimeZoneManager $timeZoneManager
     */
    public function __construct(TimeZoneManager $timeZoneManager)
    {
        $this->timeZoneManager = $timeZoneManager;
    }

    /**
     * @return View
     */
    public function getAll(): View
    {
        return $this->view($this->timeZoneManager->getAll());
    }
}