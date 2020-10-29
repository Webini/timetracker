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

    const ASSERT_MAP = [
        'should' => [
            'contains' => 'assertContains',
            'be greater than' => 'assertGreaterThan',
            'be greater than or equal to' => 'assertGreaterThanOrEqual',
            'be equal to' => 'assertEquals',
            'be less than' => 'assertLessThan',
            'be less than or equal to' => 'assertLessThanOrEqual',
        ],
        'should not' => [
            'contains' => 'assertNotContains',
            'be greater than' => 'assertNotGreaterThan',
            'be greater than or equal to' => 'assertNotGreaterThanOrEqual',
            'be equal to' => 'assertNotEquals',
            'be less than' => 'assertNotLessThan',
            'be less than or equal to' => 'assertNotLessThanOrEqual',
        ]
    ];

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

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    public function __construct(KernelInterface $kernel, RequestStack $requestStack)
    {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->bucket = [ 'user' => null ];
        $this->strictAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor()
        ;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @When /^i send a ([a-zA-Z]+) on route ([a-zA-Z0-9\-_\.?]+)$/
     * @param string $method
     * @param string $route
     * @throws \Exception
     */
    public function iSendToRoute(string $method, string $route): void
    {
        $hasRouteParams = $this->strictAccessor->isReadable($this->bucket, '[route][params]');
        $route = $this->router->generate(
            $route,
            $hasRouteParams ? $this->strictAccessor->getValue($this->bucket, '[route][params]') : []
        );

        $hasContent = $this->strictAccessor->isReadable($this->bucket, '[request][content]');
        $request = Request::create(
            $route,
            $method,
            $this->accessor->getValue($this->bucket, '[request][parameters]') ?? [],
            $this->accessor->getValue($this->bucket, '[request][cookies]') ?? [],
            $this->accessor->getValue($this->bucket, '[request][files]') ?? [],
            $this->accessor->getValue($this->bucket, '[request][server]') ?? [],
            $hasContent ? json_encode($this->strictAccessor->getValue($this->bucket, '[request][content]')) : null
        );

        if ($hasContent) {
            $request->headers->set('content-type', 'application/json');
        }

        if ($this->strictAccessor->isReadable($this->bucket, '[request][headers]')) {
            $headers = $this->strictAccessor->getValue($this->bucket, '[request][headers]');
            foreach ($headers as $key => $value) {
                $request->headers->set($key, $value);
            }
        }

        if ($this->bucket['user'] !== null) {
            $request->headers->set('X-Authorization', $this->userManager->getJwt($this->bucket['user']));
        }

        $this->response = $this->kernel->handle($request);
    }

    /**
     * @When /^i am an user of type (admin|super admin|project manager|user)$/
     * @param string $type
     */
    public function iAmAnUserOfType(string $type): void
    {
        $this->bucket['user'] = $this->getUserByType($type);
    }

    /**
     * @When /^an user of type (admin|super admin|project manager|user) saved in (.+)$/
     * @param string $type
     * @param string $path
     */
    public function addFakeUser(string $type, string $path): void
    {
        $user = $this->createFakeUserByType($type);
        $this->accessor->setValue($this->bucket, $path, $user);
    }

    /**
     * @When /^i set my jwt value to (.+)$/
     * @param string $bucket
     * @param string $key
     */
    public function iSetMyJwtTo(string $path): void
    {
        if ($this->bucket['user'] === null) {
            throw new RuntimeException('No user selected');
        }

        $this->accessor->setValue(
            $this->bucket, $path,
            $this->userManager->getJwt($this->bucket['user'])
        );
    }

    /**
     * @When /^i set my refresh token value to (.+)$/
     * @param string $path
     */
    public function iSetMyRefreshTokenTo( string $path): void
    {
        if ($this->bucket['user'] === null) {
            throw new RuntimeException('No user selected');
        }

        $this->requestStack->push(Request::create('/'));
        $this->accessor->setValue(
            $this->bucket, $path,
            $this->userManager->getRefreshToken($this->bucket['user'])
        );
        $this->requestStack->pop();
    }

    /**
     * @When /^i set to ([^\s]+) the value of ([^\s]+)$/
     * @param string $path
     * @param string $valuePath
     */
    public function iSetValueWithValue(string $path, string $valuePath): void
    {
        $readable = $this->strictAccessor->isReadable($this->bucket, $valuePath);
        if (!$readable) {
            throw new \RuntimeException('Cannot read value ' . $valuePath);
        }
        $value = $this->strictAccessor->getValue($this->bucket, $valuePath);
        $this->accessor->setValue($this->bucket, $path, $value);
    }

    /**
     * @When /^i set to ([^\s]+) value (.+)$/
     * @param string $value
     * @param string $path
     */
    public function iSetValue(string $path, string $value): void
    {
        $this->accessor->setValue($this->bucket, $path, $this->jsonDecode($value));
    }

    /**
     * @When /^i set to ([^\s]+) values:$/
     * @param string $path
     * @param PyStringNode $content
     */
    public function iSetValuesTo(string $path, PyStringNode $content): void
    {
        $this->iSetValue($path, $content->getRaw());
    }

    /**
     * @param string $data
     * @return mixed
     */
    private function jsonDecode(string $data)
    {
        $result = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                sprintf('%s (%s), don\'t forget to use json synthax', json_last_error_msg(), $data)
            );
        }
        return $result;
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
     * @param string $condition
     * @param string $keys
     */
    public function theResponseShouldOrShouldNotHaveKeys(string $condition, string $keys): void
    {
        $keys = explode(',', $keys);
        $content = $this->getResponseContent();
        $condition = strtolower($condition);
        foreach ($keys as $key) {
            $key = trim($key);
            $readable = $this->strictAccessor->isReadable($content, $key);
            if (($condition === 'should' && !$readable) ||
                ($condition === 'should not' && $readable)) {
                throw new \RuntimeException(sprintf(
                    'The key %s %s be present in response %s',
                    $key,
                    $condition,
                    var_export($content, true)
                ));
            }
        }
    }

    /**
     * @Then /^the response item ([^\s]+) (should|should not) be empty$/
     * @param string $key
     * @param string $condition
     */
    public function theResponseItemShouldOrShouldNotBeEmpty(string $key, string $condition)
    {
        $content = $this->getResponseContent();
        if (!$this->strictAccessor->isReadable($content, $key)) {
            throw new \RuntimeException(sprintf(
                'Cannot found element %s in response %s',
                $key,
                var_export($content, true)
            ));
        }

        $value = $this->strictAccessor->getValue($content, $key);
        $condition = strtolower($condition);

        if ($condition === 'should') {
            Assert::assertEmpty($value);
        } else if ($condition === 'should not') {
            Assert::assertNotEmpty($value);
        } else {
            throw new \RuntimeException('Invalid condition');
        }
    }

    /**
     * @Then /^the response item ([^\s]+) (should|should not) (contains|be greater than|be greater than or equal to|be equal to|be less than|be less than or equal to) (.+?)$/
     */
    public function theResponseItemShouldOrShouldNotBe(string $key, string $condition, string $assertExpr, string $expected): void
    {
        $expected = $this->jsonDecode($expected);

        $content = $this->getResponseContent();
        if (!$this->strictAccessor->isReadable($content, $key)) {
            throw new \RuntimeException(sprintf(
                'Cannot found element %s in response %s',
                $key,
                var_export($content, true)
            ));
        }

        $value = $this->strictAccessor->getValue($content, $key);
        $condition = strtolower($condition);

        $assert = $this->strictAccessor->getValue(self::ASSERT_MAP, '[' . $condition . '][' . $assertExpr . ']');
        Assert::$assert($expected, $value);
    }

    /**
     * @return mixed
     */
    private function getResponseContent()
    {
        return $this->jsonDecode($this->response->getContent());
    }

}