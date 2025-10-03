<?php

namespace App\Tests\Controller;

use App\Controller\BookingCreateController;
use App\Service\BookingCreateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test block responsible for creating booking cancellations
 */
class BookingCreateControllerTest extends TestCase
{
    /**
     * Creation of Mock object to represent the BookingCreateController behaviour
     * @return BookingCreateController
     */
    private function createController(): BookingCreateController
    {
        $controller = new BookingCreateController();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(false);
        $controller->setContainer($container);

        return $controller;
    }

    /**
     * Test responsible for successful booking creation
     * (http status code 201)
     * @return void
     */
    public function testInvokeReturns201OnSuccess(): void
    {
        $data = ['name' => 'test'];
        $request = new Request([], [], [], [], [], [], json_encode($data));

        $service = $this->createMock(BookingCreateService::class);
        $service->expects($this->once())
            ->method('execute')
            ->with($data);

        $controller = $this->createController();
        $response = $controller->__invoke($request, $service);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'success' => true,
                'message' => 'Booking successfuly created'
            ]),
            $response->getContent()
        );
    }

    /**
     * Test responsible for checking invalid booking JSON structure
     * (http status code 400)
     * @return void
     */
    public function testInvokeReturns400OnInvalidJson(): void
    {
        $request = new Request([], [], [], [], [], [], 'invalid json');
        $service = $this->createMock(BookingCreateService::class);

        $controller = $this->createController();
        $response = $controller->__invoke($request, $service);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test responsible for checking valid booking JSON structure
     * with wrong params
     * (http status code 422)
     * @return void
     */
    public function testInvokeReturns422OnInvalidArgumentException(): void
    {
        $data = ['name' => 'test'];
        $request = new Request([], [], [], [], [], [], json_encode($data));

        $service = $this->createMock(BookingCreateService::class);
        $service->method('execute')
            ->willThrowException(new \InvalidArgumentException('Invalid input'));

        $controller = $this->createController();
        $response = $controller->__invoke($request, $service);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => 'Invalid input'
            ]),
            $response->getContent()
        );
    }

    /**
     * Test responsible for checking internal server errors
     * (http status code 500)
     * @return void
     */
    public function testInvokeReturns500OnGenericException(): void
    {
        $data = ['name' => 'test'];
        $request = new Request([], [], [], [], [], [], json_encode($data));

        $service = $this->createMock(BookingCreateService::class);
        $service->method('execute')
            ->willThrowException(new \Exception('Unexpected error'));

        $controller = $this->createController();
        $response = $controller->__invoke($request, $service);

        $this->assertEquals(500, $response->getStatusCode());
    }
}
