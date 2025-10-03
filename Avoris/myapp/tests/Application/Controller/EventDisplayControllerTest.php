<?php

namespace App\Tests\Controller;

use App\DTO\Output\EventInfoDataDTO;
use App\Service\EventDisplayService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test block responsible for displaying concrete info events
 */
class EventDisplayControllerTest extends WebTestCase
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
     * Set a mock object in order to instance EventDisplayService class
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->mockService = $this->createMock(EventDisplayService::class);

        static::getContainer()->set(EventDisplayService::class, $this->mockService);
    }

    /**
     * Test responsible for successful display info of event
     * (http status code 200)
     * @return void
     */
    public function testInvokeReturns200OnSuccess(): void
    {
        $mockEvent = new EventInfoDataDTO();
        $mockEvent->setName('Mocked Event');
        $mockEvent->setFromDate(new \DateTimeImmutable('2025-08-10'));
   
        $this->mockService
            ->method('execute')
            ->with(['id' => 1])
            ->willReturn($mockEvent);

        $this->client->request('GET', '/events/1');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringNotEqualsJsonString(json_encode([
            'success' => 'Event displayed successfuly',
            'data' => $mockEvent,
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking event id not found
     * (http status code 404)
     * @return void
     */
    public function testInvokeReturns404WhenEventNotFound(): void
    {
        $this->mockService
            ->method('execute')
            ->with(['id' => 999])
            ->willReturn(null);

        $this->client->request('GET', '/events/999');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'success' => false,
            'message' => 'Event not found'
        ]), $response->getContent());
    }

    /**
     * Test responsible for checking internal server errors
     * (http status code 500)
     * @return void
     */
    public function testInvokeReturns500OnException(): void
    {
        $this->mockService
            ->method('execute')
            ->with(['id' => 123])
            ->willThrowException(new \Exception('Something went wrong'));

        $this->client->request('GET', '/events/123');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Internal server error', $response->getContent());
    }
}
