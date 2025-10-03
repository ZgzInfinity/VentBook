<?php

namespace App\Tests\Controller;

use App\Service\EventCreateService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Test block responsible for creating events
 */
class EventCreateControllerTest extends WebTestCase
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
     * Set a mock object in order to instance EventCreateService class
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->mockService = $this->createMock(EventCreateService::class);
        
        static::getContainer()->set(EventCreateService::class, $this->mockService);
    }

    /**
     * Test responsible for successful event creation
     * (http status code 201)
     * @return void
     */
    public function testInvokeReturns201OnSuccess(): void
    {
        $this->mockService
            ->method('execute')
            ->willReturn(123);

        $this->client->request('POST', '/events', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['name' => 'Concert', 'date' => '2025-10-01']));

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'success' => true,
            'event_id' => 123
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking invalid input event JSON structure
     * (http status code 400)
     * @return void
     */
    public function testInvokeReturns400OnInvalidJson(): void
    {
        $this->client->request('POST', '/events', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], 'invalid json');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'success' => false,
            'message' => 'Invalid event format'
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking wrong event format
     * (http status code 422)
     * @return void
     */
    public function testInvokeReturns422OnValidationException(): void
    {
        $this->mockService
            ->method('execute')
            ->willThrowException(
                new ValidationFailedException(
                    new \stdClass(),
                    new ConstraintViolationList()
                )
            );

        $this->client->request('POST', '/events', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['name' => '', 'date' => 'invalid']));

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertStringContainsString('Unknown event format', $response->getContent());
    }

    /**
     * Test responsible for checking internal server errors
     * (http status code 500)
     * @return void
     */
    public function testInvokeReturns500OnGeneralException(): void
    {
        $this->mockService
            ->method('execute')
            ->willThrowException(new \Exception("Something went wrong"));

        $this->client->request('POST', '/events', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['name' => 'Test', 'date' => '2025-01-01']));

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString('Internal server error', $response->getContent());
    }
}
