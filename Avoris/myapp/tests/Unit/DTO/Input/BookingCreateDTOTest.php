<?php

namespace App\Tests\Unit\DTO\Input;

use App\DTO\Input\BookingCreateDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTimeImmutable;

/**
 * Test block for checking Input BookingCreateDTO
 * (Data object which represents the input structure of a booking)
 */
class BookingCreateDTOTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * Set the container kernel to have available Symfony data validations
     * @return void
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Creates a mock object of BookingCreateDTO class
     * @return BookingCreateDTO
     */
    private function createValidDTO(): BookingCreateDTO
    {
        $dto = new BookingCreateDTO();
        $dto->setReference('ABC123');
        $dto->setEventId(42);
        $dto->setEventDate(new DateTimeImmutable('2025-12-31'));
        $dto->setAttendees(3);
        $dto->setIdentification('1234567873HA');
        return $dto;
    }

    /**
     * Test responsible for checking if DTO structure
     * according to constraints is correct
     * @return void
     */
    public function testValidDTOIsValid(): void
    {
        $dto = $this->createValidDTO();
        $errors = $this->validator->validate($dto);
        $this->assertCount(0, $errors, 'DTO válido no debería tener errores');
    }

    /**
     * Test responsible for checking if reference field can be blank ('')
     * @return void
     */
    public function testBlankReferenceIsInvalid(): void
    {
        $dto = $this->createValidDTO();
        $dto->setReference('');
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * Test responsible for checking if attendees of
     * a booking are greater than zero
     * @return void
     */
    public function testAttendeesMustBeGreaterThanOne(): void
    {
        $dto = $this->createValidDTO();
        $dto->setAttendees(1);
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * Test responsible for checking if quantity attendees
     * is valid (positive)
     * @return void
     */
    public function testNegativeAttendeesIsInvalid(): void
    {
        $dto = $this->createValidDTO();
        $dto->setAttendees(-5);
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }
}
