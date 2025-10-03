<?php

namespace App\Tests\Controller;

use App\Controller\BookingListingController;
use App\Service\BookingListingService;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test block responsible for listing bookings of user
 */
class BookingListingControllerTest extends KernelTestCase
{
    private function getController(): BookingListingController
    {
        self::bootKernel();
        return static::getContainer()->get(BookingListingController::class);
    }

    /**
     * Test responsible for checking when booking is listed correctly
     * @return void
     */
    public function testInvokeReturns200OnSuccess(): void
    {
        $dummyBookings = [['reference' => 1], ['reference' => 2]];

        $service = $this->createMock(BookingListingService::class);
        $service->method('execute')
            ->with(['id' => 'abc123'])
            ->willReturn($dummyBookings);

        $serializer = $this->getMockBuilder(\Symfony\Component\Serializer\Serializer::class)
            ->onlyMethods(['normalize'])
            ->getMock();

        $serializer->method('normalize')->willReturn($dummyBookings);

        $controller = $this->getController();
        $response = $controller->__invoke('abc123', $service, $serializer);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'success' => true,
                'message' => $dummyBookings
            ]),
            $response->getContent()
        );
    }

    /**
     * Test responsible for checking wrong input booking reference format
     * @return void
     */
    public function testInvokeReturns400OnMissingId(): void
    {
        $service = $this->createMock(BookingListingService::class);
        $serializer = $this->createMock(SerializerInterface::class);

        $controller = $this->getController();
        $response = $controller->__invoke('', $service, $serializer);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test responsible for checking unknown input booking reference format
     * @return void
     */
    public function testInvokeReturns422OnNullResult(): void
    {
        $service = $this->createMock(BookingListingService::class);
        $service->method('execute')
            ->with(['id' => 'abc123'])
            ->willReturn(null);

        $serializer = $this->createMock(SerializerInterface::class);

        $controller = $this->getController();
        $response = $controller->__invoke('abc123', $service, $serializer);

        $this->assertEquals(422, $response->getStatusCode());
    }
}
