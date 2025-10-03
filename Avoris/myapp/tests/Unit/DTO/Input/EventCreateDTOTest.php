<?php

namespace App\Tests\Unit\DTO\Input;

use App\DTO\Input\EventCreateDTO;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Test block for checking Input EventCreateDTO
 * (Data object which represents the input structure of an event)
 */
class EventCreateDTOTest extends KernelTestCase
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
     * Creates a mock object of EventCreateDTO class
     * @return EventCreateDTO
     */
    private function getValidDTO(): EventCreateDTO
    {
        $dto = new EventCreateDTO();
        $dto->setName('Symfony Conference');
        $dto->setDescription('A community event for Symfony developers.');
        $dto->setFromDate(new DateTimeImmutable('2025-09-01 09:00'));
        $dto->setToDate(new DateTimeImmutable('2025-09-01 17:00'));
        $dto->setAvailableSeats(100);
        return $dto;
    }

    /**
     * Test responsible for checking if DTO structure
     * according to constraints is correct
     * @return void
     */
    public function testValidDTOIsValid(): void
    {
        $dto = $this->getValidDTO();
        $errors = $this->validator->validate($dto);
        $this->assertCount(0, $errors);
    }

    /**
     * Test responsible for checking if name field can be blank ('')
     * @return void
     */
    public function testBlankNameIsInvalid(): void
    {
        $dto = $this->getValidDTO();
        $dto->setName('');
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * Test responsible for checking if name field
     * has more than 256 characters (limit 255)
     * @return void
     */
    public function testNameTooLongIsInvalid(): void
    {
        $dto = $this->getValidDTO();
        $dto->setName(str_repeat('A', 256)); // 256 chars
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * Test responsible for checking if description field can be blank ('')
     * @return void
     */
    public function testBlankDescriptionIsInvalid(): void
    {
        $dto = $this->getValidDTO();
        $dto->setDescription('');
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * Test responsible for checking if starting date is
     * always lower or equal than ending date
     * @return void
     */
    public function testFromDateAfterToDateIsInvalid(): void
    {
        $dto = $this->getValidDTO();
        $dto->setFromDate(new DateTimeImmutable('2025-09-02 09:00'));
        $dto->setToDate(new DateTimeImmutable('2025-09-01 09:00'));
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
        $this->assertEquals('The start date must be less than or equal to the end date.', $errors[0]->getMessage());
    }

    /**
     * Test responsible for checking if available seats can be negative
     * @return void
     */
    public function testNegativeAvailableSeatsIsInvalid(): void
    {
        $dto = $this->getValidDTO();
        $dto->setAvailableSeats(-5);
        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * Test responsible for checking if available seats can be zero
     * @return void
     */
    public function testAvailableSeatsZeroIsValid(): void
    {
        $dto = $this->getValidDTO();
        $dto->setAvailableSeats(0);
        $errors = $this->validator->validate($dto);
        $this->assertCount(0, $errors);
    }

    /**
     * Test responsible for checking if event date is correctly formatted
     * @return void
     */
    public function testToDateEqualsFromDateIsValid(): void
    {
        $dto = $this->getValidDTO();
        $sameDate = new DateTimeImmutable('2025-09-01 10:00');
        $dto->setFromDate($sameDate);
        $dto->setToDate($sameDate);
        $errors = $this->validator->validate($dto);
        $this->assertCount(0, $errors);
    }
}
