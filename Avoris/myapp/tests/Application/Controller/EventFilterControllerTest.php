<?php

namespace App\Tests\Controller;

use App\Service\EventOptionalFilterDisplayService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test block responsible for filter events
 * according to searching filters
 */
class EventFilterControllerTest extends WebTestCase
{
    /**
     * @var $client
     */
    private $client;

    /**
     * @var $mockService
     */
    private $mockService;

    /**
     * Set a mock object in order to instance EventOptionalFilterDisplayService class
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->mockService = $this->createMock(EventOptionalFilterDisplayService::class);

        static::getContainer()->set(EventOptionalFilterDisplayService::class, $this->mockService);
    }

    /**
     * Test responsible for successful listing events
     * according to some filter options
     * (http status code 200)
     * @return void
     */
    public function testInvokeReturns200OnSuccess(): void
    {
        $this->mockService
            ->method('execute')
            ->willReturn([['id' => 1, 'name' => 'Concert']]);

        $this->client->request('GET', '/events');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'success' => true,
            'message' => [['id' => 1, 'name' => 'Concert']]
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking invalid filters JSON structure
     * (http status code 400)
     * @return void
     */
    public function testInvokeReturns400OnInvalidFiltersJson(): void
    {
        $this->client->request('GET', '/events', [
            'filters' => '{invalid json}'
        ]);

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'success' => false,
            'message' => 'Invalid filters format'
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking filtered event lit not found
     * (http status code 404)
     * @return void
     */
    public function testInvokeReturns404WhenNoEvents(): void
    {
        $this->mockService
            ->method('execute')
            ->willReturn(null);

        $this->client->request('GET', '/events');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'success' => true,
            'message' => 'No events found'
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking wrong filters format
     * (http status code 422)
     * @return void
     */
    public function testInvokeReturns422OnInvalidArgument(): void
    {
        $this->mockService
            ->method('execute')
            ->willThrowException(new \InvalidArgumentException("Bad filters"));

        $this->client->request('GET', '/events', [
            'filters' => json_encode(['unknown' => 'value'])
        ]);

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'success' => false,
            'message' => 'Unknown filters params'
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking internal server errors
     * (http status code 500)
     * @return void
     */
    public function testInvokeReturns500OnThrowable(): void
    {
        $this->mockService
            ->method('execute')
            ->willThrowException(new \RuntimeException("Something went wrong"));

        // Simulamos fallo genérico en try/catch exterior
        $this->client->request('GET', '/events');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString('Internal server error', $response->getContent());
    }
}
