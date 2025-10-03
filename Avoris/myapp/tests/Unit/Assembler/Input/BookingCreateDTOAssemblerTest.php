<?php

namespace App\Tests\Unit\Assembler\Input;

use App\Assembler\Input\BookingCreateDTOAssembler;
use App\DTO\Input\BookingCreateDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Test block responsible for checking correct assembling of BookingCreateDTO
 * (Data Transfer object which represents the input data structure of a booking)
 */
class BookingCreateDTOAssemblerTest extends TestCase
{
    /**
     * Test responsible for checking if BookingCreateDTO is correctly
     * assembled according to constraints and restrictions
     * @return void
     * @throws \Exception
     */
    public function testTransformWithValidDataReturnsDTO(): void
    {
        $params = [
            'reference' => 'ABC123',
            'event_id' => 42,
            'event_date' => '2025-10-01',
            'attendees' => 3,
            'identification' => '12345678Z',
        ];

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $assembler = new BookingCreateDTOAssembler($validator);
        $dto = $assembler->transform($params);

        $this->assertInstanceOf(BookingCreateDTO::class, $dto);
        $this->assertEquals($params['reference'], $dto->getReference());
        $this->assertEquals($params['event_id'], $dto->getEventId());
        $this->assertEquals(new DateTimeImmutable($params['event_date']), $dto->getEventDate());
        $this->assertEquals($params['attendees'], $dto->getAttendees());
        $this->assertEquals($params['identification'], $dto->getIdentification());
    }

    /**
     * Test responsible for checking if identification nullable constraint works properly
     * (Bookings always must have identification - user id assigned)
     * @return void
     */
    public function testTransformWithInvalidDataThrowsValidationFailedException(): void
    {
        $params = [
            'reference' => '',
            'event_id' => 1,
            'event_date' => '2025-01-01',
            'attendees' => -1,
            'identification' => '',
        ];

        $violation = new ConstraintViolation(
            message: 'This value should not be blank.',
            messageTemplate: '',
            parameters: [],
            root: null,
            propertyPath: 'reference',
            invalidValue: '',
        );

        $violations = new ConstraintViolationList([$violation]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $assembler = new BookingCreateDTOAssembler($validator);

        $this->expectException(ValidationFailedException::class);

        $assembler->transform($params);
    }

    /**
     * Test responsible for checking if BookingCreateDTO fields
     * are correctly initialized
     * @return void
     */
    public function testTransformSetsAllFieldsCorrectly(): void
    {
        $params = [
            'reference' => 'XYZ789',
            'event_id' => 100,
            'event_date' => '2025-12-31',
            'attendees' => 5,
            'identification' => '87654321X',
        ];

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $assembler = new BookingCreateDTOAssembler($validator);
        $dto = $assembler->transform($params);

        $this->assertEquals('XYZ789', $dto->getReference());
        $this->assertEquals(100, $dto->getEventId());
        $this->assertEquals('2025-12-31', $dto->getEventDate()->format('Y-m-d'));
        $this->assertEquals(5, $dto->getAttendees());
        $this->assertEquals('87654321X', $dto->getIdentification());
    }
}
