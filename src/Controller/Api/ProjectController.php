<?php


namespace App\Controller\Api;


use App\Form\Entity\ProjectType;
use App\Security\Voter\ProjectVoter;
use App\Traits\EntityManagerAwareTrait;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractFOSRestController
{
    use EntityManagerAwareTrait;
    use SubmitFormTrait;

    /**
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $this->denyAccessUnlessGranted(ProjectVoter::PROJECT_CREATE);

        $form = $this->createForm(ProjectType::class);
        $this->submitRequestContent($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();

            $this->em->persist($project);
            $this->em->flush();

            return $this->view($project);
        }

        return $this->view($form);
    }
}