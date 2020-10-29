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
use PHPUnit\Framework\Assert;

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
     * @When /^i send a ([a-zA-Z]+) on route ([a-zA-Z0-9\-_\.]+)$/
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
     * @When /^i am an user of type (.+)$/
     * @param string $type
     */
    public function iAmAnUserOfType(string $type): void
    {
        $this->user = $this->getUserByType($type);
        if ($this->user === null) {
            throw new \RuntimeException('Cannot found user of type ' . $type);
        }
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
     * @Then /^the response (should|should not) be successful$/
     */
    public function theResponseShouldOrShouldNotBeSuccessful(string $condition): void
    {
        $statusCode = $this->response->getStatusCode();
        $condition = strtolower($condition);
        $firstDigit = substr((string)$statusCode, 0, 1);
        if (($condition === 'should' && $firstDigit !== '2') ||
            ($condition === 'should not' && $firstDigit === '2')) {
            throw new \RuntimeException(sprintf(
                "Invalid status code received : %d\n%s",
                $statusCode,
                var_export($this->getResponseContent(), true)
            ));
        }
    }

    /**
     * @Then /^the status code (should|should not) be ([0-9]+)$/
     * @param int $statusCode
     */
    public function theStatusCodeShouldOrShouldNotBe(string $condition, int $statusCode): void
    {
        $condition = strtolower($condition);
        $responseStatusCode = $this->response->getStatusCode();
        if (($condition === 'should' && $statusCode !== $responseStatusCode) ||
            ($condition === 'should not' && $statusCode === $responseStatusCode)) {
            throw new \RuntimeException(sprintf(
                'Invalid status code %s, it %s be %d',
                $this->response->getStatusCode(),
                $condition,
                $statusCode
            ));
        }
    }

    /**
     * @Then /^the response (should|should not) have keys (.+)$/
     * @param $keys
     */
    public function theResponseShouldOrShouldNotHaveKeys(string $condition, string $keys): void
    {
        $keys = explode(',', $keys);
        $content = $this->getResponseContent();
        $condition = strtolower($condition);
        foreach ($keys as $key) {
            $key = trim($key);
            $readable = $this->accessor->isReadable($content, $key);
            if (($condition === 'should' && !$readable) ||
                ($condition === 'should not' && $readable)) {
                throw new \RuntimeException(sprintf(
                    'The key %s %s present in response %s',
                    $key,
                    $condition,
                    var_export($content, true)
                ));
            }
        }
    }

    /**
     * @Then /^the response item (.+?) (should|should not) be (.+?)$/
     */
    public function theResponseItemShouldOrShouldNotBe(string $key, string $condition, string $expected): void
    {
        $expected = json_decode($expected, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                sprintf('Cannot parse expected value (%s), don\'t forget to use json synthax', json_last_error_msg())
            );
        }

        $content = $this->getResponseContent();
        if (!$this->accessor->isReadable($content, $key)) {
            throw new \RuntimeException(sprintf(
                'Cannot found element %s in response %s',
                $key,
                var_export($content, true)
            ));
        }

        $value = $this->accessor->getValue($content, $key);
        $condition = strtolower($condition);

        if ($condition === 'should') {
            Assert::assertEquals($expected, $value);
        }
        if ($condition === 'should not') {
            Assert::assertNotEquals($expected, $value);
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