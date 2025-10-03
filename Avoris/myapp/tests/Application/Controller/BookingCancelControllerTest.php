<?php

namespace App\Tests\Controller;

use App\Controller\BookingCancelController;
use App\Service\BookingCancelService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Test block responsible for checking booking cancellations
 */
class BookingCancelControllerTest extends TestCase
{
    /**
     * Test responsible for checking successful booking cancellation
     * (http status code 200)
     * @return void
     */
    public function testInvokeReturns200OnSuccess(): void
    {
        $bookingCancelService = $this->createMock(BookingCancelService::class);
        $bookingCancelService->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $controller = new BookingCancelController();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(false);

        $controller->setContainer($container);

        $response = $controller->__invoke(1, $bookingCancelService);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test responsible for checking invalid booking
     * cancellation request (invalid id format)
     * (http status code 400)
     * @return void
     */
    public function testInvokeReturns400OnBadRequest(): void
    {
        $bookingCancelService = $this->createMock(BookingCancelService::class);
        $bookingCancelService->method('execute')
            ->willThrowException(new BadRequestHttpException('Invalid ID'));

        $controller = new BookingCancelController();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(false);
        $controller->setContainer($container);

        $response = $controller->__invoke(999, $bookingCancelService);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test responsible for checking wrong booking
     * cancellation request (not found)
     * (http status code 404)
     * @return void
     */
    public function testInvokeReturns404OnNotFound(): void
    {
        $bookingCancelService = $this->createMock(BookingCancelService::class);
        $bookingCancelService->method('execute')
            ->willThrowException(new NotFoundHttpException('Booking not found'));

        $controller = new BookingCancelController();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(false);
        $controller->setContainer($container);

        $response = $controller->__invoke(1, $bookingCancelService);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test responsible for checking internal server errrors
     * (http status code 500)
     * @return void
     */
    public function testInvokeReturns500OnGeneralException(): void
    {
        $bookingCancelService = $this->createMock(BookingCancelService::class);
        $bookingCancelService->method('execute')
            ->willThrowException(new \Exception('Unexpected'));

        $controller = new BookingCancelController();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(false);
        $controller->setContainer($container);

        $response = $controller->__invoke(1, $bookingCancelService);

        $this->assertEquals(500, $response->getStatusCode());
    }
}
