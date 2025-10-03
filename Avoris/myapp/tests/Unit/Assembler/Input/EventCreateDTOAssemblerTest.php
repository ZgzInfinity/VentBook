<?php

namespace App\Tests\Unit\Assembler\Input;

use App\Assembler\Input\EventCreateDTOAssembler;
use App\DTO\Input\EventCreateDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Test block responsible for checking correct assembling of EventCreateDTO
 * (Data Transfer object which represents the input data structure of an event)
 */
class EventCreateDTOAssemblerTest extends TestCase
{
    /**
     * Test responsible for checking if EventCreateDTO is correctly
     * assembled according to constraints and restrictions
     * @return void
     */
    public function testTransformWithValidData(): void
    {
        $params = [
            'name' => 'Concert',
            'description' => 'Live concert event',
            'from_date' => '2025-09-01',
            'to_date' => '2025-09-02',
            'available_seats' => 100,
        ];

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $assembler = new EventCreateDTOAssembler($validator);

        $dto = $assembler->transform($params);

        $this->assertInstanceOf(EventCreateDTO::class, $dto);
        $this->assertSame('Concert', $dto->getName());
        $this->assertSame('Live concert event', $dto->getDescription());
        $this->assertEquals(new DateTimeImmutable('2025-09-01'), $dto->getFromDate());
        $this->assertEquals(new DateTimeImmutable('2025-09-02'), $dto->getToDate());
        $this->assertSame(100, $dto->getAvailableSeats());
    }

    /**
     * Test responsible for checking if EventCreateDTO reports
     * ValidationFailedException when fields are not properly established
     * @return void
     */
    public function testTransformWithInvalidDataThrowsValidationFailedException(): void
    {
        $params = [
            'name' => '', // Invalid: NotBlank
            'description' => '', // Valid nullable
            'from_date' => '2025-09-01',
            'to_date' => '2025-09-02',
            'available_seats' => -10, // Invalid: negative
        ];

        $violation = new ConstraintViolation(
            'This value should not be blank.',
            '',
            [],
            null,
            'name',
            ''
        );

        $violations = new ConstraintViolationList([$violation]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $assembler = new EventCreateDTOAssembler($validator);

        $this->expectException(ValidationFailedException::class);

        $assembler->transform($params);
    }
}
