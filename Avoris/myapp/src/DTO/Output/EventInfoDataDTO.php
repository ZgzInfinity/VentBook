<?php

namespace App\DTO\Output;

use DateTimeImmutable;

/**
 * DTO used to return event information in API responses.
 */
class EventInfoDataDTO
{
    /**
     * @var string Event name
     */
    private string $name;

    /**
     * @var string|null Event description
     */
    private ?string $description = null;

    /**
     * @var DateTimeImmutable Event start date
     */
    private DateTimeImmutable $fromDate;

    /**
     * @var DateTimeImmutable Event end date
     */
    private DateTimeImmutable $toDate;

    /**
     * @var int Available seats
     */
    private int $availableSeats;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $_name
     */
    public function setName(?string $_name): void
    {
        $this->name = $_name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $_description
     */
    public function setDescription(?string $_description): void
    {
        $this->description = $_description;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getFromDate(): DateTimeImmutable
    {
        return $this->fromDate;
    }

    /**
     * @param DateTimeImmutable $_fromDate
     */
    public function setFromDate(DateTimeImmutable $_fromDate): void
    {
        $this->fromDate = $_fromDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getToDate(): DateTimeImmutable
    {
        return $this->toDate;
    }

    /**
     * @param DateTimeImmutable $_toDate
     */
    public function setToDate(DateTimeImmutable $_toDate): void
    {
        $this->toDate = $_toDate;
    }

    /**
     * @return int
     */
    public function getAvailableSeats(): int
    {
        return $this->availableSeats;
    }

    /**
     * @param int $_availableSeats
     */
    public function setAvailableSeats(int $_availableSeats): void
    {
        $this->availableSeats = $_availableSeats;
    }
}
