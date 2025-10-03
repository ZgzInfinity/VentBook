<?php

namespace App\DTO\Input;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

/**
 * Data Transfer Object for creating an event.
 * 
 * Contains the necessary data and validation rules for event creation.
 */
class EventCreateDTO
{
    /**
     * Name of the event.
     *
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $name;

    /**
     * Description of the event.
     *
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    public string $description;

    /**
     * Start date of the event.
     *
     * @var DateTimeImmutable
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public DateTimeImmutable $fromDate;

    /**
     * End date of the event.
     * Must be greater than or equal to the start date.
     *
     * @var DateTimeImmutable
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Expression(
        "this.fromDate <= this.toDate",
        message: "The start date must be less than or equal to the end date."
    )]
    public DateTimeImmutable $toDate;

    /**
     * Number of available seats for the event.
     *
     * @var int
     */
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\PositiveOrZero]
    public int $availableSeats;

    /**
     * Gets the event name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the event name.
     *
     * @param string $_name
     * @return void
     */
    public function setName(string $_name): void
    {
        $this->name = $_name;
    }

    /**
     * Gets the event description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the event description.
     *
     * @param string $_description
     * @return void
     */
    public function setDescription(string $_description): void
    {
        $this->description = $_description;
    }

    /**
     * Gets the event start date.
     *
     * @return DateTimeImmutable
     */
    public function getFromDate(): DateTimeImmutable
    {
        return $this->fromDate;
    }

    /**
     * Sets the event start date.
     *
     * @param DateTimeImmutable $_fromDate
     * @return void
     */
    public function setFromDate(DateTimeImmutable $_fromDate): void
    {
        $this->fromDate = $_fromDate;
    }

    /**
     * Gets the event end date.
     *
     * @return DateTimeImmutable
     */
    public function getToDate(): DateTimeImmutable
    {
        return $this->toDate;
    }

    /**
     * Sets the event end date.
     *
     * @param DateTimeImmutable $_toDate
     * @return void
     */
    public function setToDate(DateTimeImmutable $_toDate): void
    {
        $this->toDate = $_toDate;
    }

    /**
     * Gets the number of available seats.
     *
     * @return int
     */
    public function getAvailableSeats(): int
    {
        return $this->availableSeats;
    }

    /**
     * Sets the number of available seats.
     *
     * @param int $_availableSeats
     * @return void
     */
    public function setAvailableSeats(int $_availableSeats): void
    {
        $this->availableSeats = $_availableSeats;
    }
}
