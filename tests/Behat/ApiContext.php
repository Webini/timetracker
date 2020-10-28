<?php


namespace App\Tests\Behat;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Tests\Behat\Traits\RouterAwareTrait;
use App\Tests\Behat\Traits\UserTrait;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class ApiContext implements Context
{
    use UserTrait;
    use RouterAwareTrait;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var array
     */
    private $bucket;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(KernelInterface $kernel, RequestStack $requestStack)
    {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->user = null;
        $this->bucket = [];
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor()
        ;
    }

    /**
     * @When /^i send a ([a-zA-Z]+) on route ([a-zA-Z0-9\-_\.?]+)$/
     * @param string $method
     * @param string $route
     * @throws \Exception
     */
    public function iSendToRoute(string $method, string $route): void
    {
        $this->iSendToRouteWithParameters($method, $route);
    }

    /**
     * @When /^i send a ([a-zA-Z]+) on route ([a-zA-Z0-9\-_\.?]+) with parameters:$/
     * @param string $method
     * @param string $route
     * @param PyStringNode|null $strParameters
     * @throws \Exception
     */
    public function iSendToRouteWithParameters(string $method, string $route, ?PyStringNode $strParameters = null): void
    {
        $parameters = [];
        if ($strParameters != null) {
            $parameters = json_decode($strParameters->getRaw(), true);
        }

        $route = $this->router->generate($route, $parameters);
        $hasContent = isset($this->bucket['content']);
        $request = Request::create(
            $route,
            $method,
            isset($this->bucket['parameters']) ? $this->bucket['parameters'] : [],
            [], [], [],
            $hasContent ? json_encode($this->bucket['content']) : null
        );

        if ($hasContent) {
            $request->headers->set('content-type', 'application/json');
        }

        if (isset($this->bucket['headers'])) {
            foreach ($this->bucket['headers'] as $key => $value) {
                $request->headers->set($key, $value);
            }
        }

        if ($this->user !== null) {
            $request->headers->set('X-Authorization', $this->userManager->getJwt($this->user));
        }

        $this->response = $this->kernel->handle($request);
    }

    /**
     * @When /^i am an user of type ([a-zA-Z]+)$/
     * @param string $type
     */
    public function iAmAnUserOfType(string $type): void
    {
        $this->user = $this->getUserByType($type);
    }

    /**
     * @When /^i add to (content|parameters|headers) a key ([a-zA-Z0-9\-_\.]+) with my jwt value$/
     * @param string $bucket
     * @param string $key
     */
    public function iAddJwtTo(string $bucket, string $key): void
    {
        if ($this->user === null) {
            throw new RuntimeException('No user selected');
        }

        $this->iAddToValueWithCast($bucket, $key, null, $this->userManager->getJwt($this->user));
    }

    /**
     * @When /^i add to (content|parameters|headers) a key ([a-zA-Z0-9\-_\.]+) with my refresh token value$/
     * @param string $bucket
     * @param string $key
     */
    public function iAddRefreshTokenTo(string $bucket, string $key): void
    {
        if ($this->user === null) {
            throw new RuntimeException('No user selected');
        }

        $this->requestStack->push(Request::create('/'));
        $this->iAddToValueWithCast($bucket, $key, null, $this->userManager->getRefreshToken($this->user));
        $this->requestStack->pop();
    }

    /**
     * @When /^i add to (content|parameters|headers) a key ([a-zA-Z0-9\-_\.]+) with value (.+)$/
     * @param string $bucket
     * @param string $key
     * @param string $value
     */
    public function iAddValueTo(string $bucket, string $key, string $value): void
    {
        $this->iAddToValueWithCast($bucket, $key, null, $value);
    }

    /**
     * @When /^i add to (content|parameters|headers) a key ([a-zA-Z0-9\-_\.]+) with ((boolean|bool|integer|int|float|double|string|null)?) value (.+)$/
     * @param string $bucket
     * @param string $key
     * @param string|null $cast
     * @param string $value
     */
    public function iAddToValueWithCast(string $bucket, string $key, ?string $cast, string $value): void
    {
        if (!isset($this->bucket[$bucket])) {
            $this->bucket[$bucket] = [];
        }

        $this->bucket[$bucket][$key] = $value;
        if ($cast !== null) {
            settype($this->bucket[$bucket][$key], $cast);
        }
    }

    /**
     * @When /^i add to (content|parameters|headers) values:$/
     * @param string $bucket
     * @param PyStringNode $content
     */
    public function iAddToValues(string $bucket, PyStringNode $content): void
    {
        if (!isset($this->bucket[$bucket])) {
            $this->bucket[$bucket] = [];
        }

        $this->bucket[$bucket] = array_merge(
            $this->bucket[$bucket],
            json_decode($content->getRaw(), true)
        );
    }

    /**
     * @Then /^the response should be successful$/
     */
    public function theResponseShouldBeSuccessful(): void
    {
        $statusCode = $this->response->getStatusCode();
        if (substr((string)$statusCode, 0, 1) !== "2") {
            throw new \RuntimeException(sprintf(
                "Invalid status code received : %d\n%s",
                $statusCode,
                var_export($this->getResponseContent(), true)
            ));
        }
    }

    /**
     * @Then /^the response should not be successful$/
     */
    public function theResponseShouldNotBeSuccessful(): void
    {
        $statusCode = $this->response->getStatusCode();
        if (substr((string)$statusCode, 0, 1) === "2") {
            throw new \RuntimeException(sprintf(
                "Invalid status code received : %d\n%s",
                $statusCode,
                var_export($this->getResponseContent(), true)
            ));
        }
    }

    /**
     * @Then /^the status code should be ([0-9]+)$/
     * @param int $statusCode
     */
    public function theStatusCodeShouldBe(int $statusCode): void
    {
        if ($statusCode !== $this->response->getStatusCode()) {
            throw new \RuntimeException(sprintf(
                'Invalid status code %s expected %d',
                $this->response->getStatusCode(),
                $statusCode
            ));
        }
    }

    /**
     * @Then /^the response should have keys (.*)$/
     * @param $keys
     */
    public function theResponseShouldHaveKeys(string $keys): void
    {
        $keys = explode(',', $keys);
        $content = $this->getResponseContent();
        foreach ($keys as $key) {
            $key = trim($key);
            if (!$this->accessor->isReadable($content, $key)) {
                throw new \RuntimeException(sprintf(
                    'The key %s be present in response %s',
                    $key,
                    var_export($content, true)
                ));
            }
        }
    }

    /**
     * @Then /^the response should not have keys (.*)$/
     * @param string $keys
     */
    public function theResponseShouldNotHaveKeys(string $keys): void
    {
        $keys = explode(',', $keys);
        $content = $this->getResponseContent();
        foreach ($keys as $key) {
            $key = trim($key);
            if ($this->accessor->isReadable($content, $key)) {
                throw new \RuntimeException(sprintf(
                    'The key %s should not be present in response %s',
                    $key,
                    var_export($content, true)
                ));
            }
        }
    }

    /**
     * @return mixed
     */
    private function getResponseContent()
    {
        $content = $this->response->getContent();
        $parsedContent = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf('Cannot parse content %s', json_last_error_msg()));
        }
        return $parsedContent;
    }

}