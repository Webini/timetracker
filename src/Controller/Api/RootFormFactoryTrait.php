<?php


namespace App\Controller\Api;


use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

trait RootFormFactoryTrait
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @required
     * @param FormFactoryInterface $formFactory
     * @return $this
     */
    public function setFormFactory(FormFactoryInterface $formFactory): self
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * @param string|FormInterface $type
     * @param null $data
     * @param array $options
     * @return FormInterface
     */
    protected function createRootForm($type, $data = null, array $options = array()): FormInterface
    {
        return $this->formFactory->createNamed('', $type, $data, $options);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param bool $clearMissing
     * @return FormInterface
     */
    protected function submitRequestContent(FormInterface $form, Request $request, bool $clearMissing = true): FormInterface
    {
        $data = null;
        $contentType = $request->getContentType();
        if ($contentType !== 'json') {
            throw new NotAcceptableHttpException('Invalid content type, it must be application/json');
        }

        $requestContent = $request->getContent();
        if (empty($requestContent)) {
            throw new NotAcceptableHttpException('Empty content');
        }

        $content = json_decode($requestContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid content : ' . json_last_error_msg());
        }

        $data = $this->replaceBooleans($content);
        return $form->submit($data, $clearMissing);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param bool $clearMissing
     * @return FormInterface
     */
    protected function submitRequestQuery(FormInterface $form, Request $request, bool $clearMissing = true): FormInterface
    {
        return $form->submit($request->query->all(), $clearMissing);
    }

    /**
     * Replace boolean by 1 or 0 in order to avoid symfony form data cast to string
     * @param mixed $data
     * @return array|int
     */
    private function replaceBooleans($data)
    {
        if (is_array($data)) {
            $output = [];

            foreach ($data as $key => $value) {
                $output[$key] = $this->replaceBooleans($value);
            }

            return $output;
        }

        if (is_bool($data)) {
            return $data ? 1 : 0;
        }

        return $data;
    }
}