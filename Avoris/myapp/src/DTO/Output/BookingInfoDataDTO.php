<?php

namespace App\DTO\Output;

use DateTimeImmutable;

/**
 * DTO used to expose booking information in API responses.
 */
class BookingInfoDataDTO
{
    /**
     * @var string Identification of the buyer (DNI)
     */
    private string $identification;

    /**
     * @var string Unique booking reference
     */
    private string $reference;

    /**
     * @var int ID of the related event
     */
    private int $eventId;

    /**
     * @var DateTimeImmutable Selected event date
     */
    private DateTimeImmutable $eventDate;

    /**
     * @var int Number of attendees included in the booking
     */
    private int $attendees;

    /**
     * @return string
     */
    public function getIdentification(): string
    {
        return $this->identification;
    }

    /**
     * @param string $_identification
     */
    public function setIdentification(string $_identification): void
    {
        $this->identification = $_identification;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $_reference
     */
    public function setReference(string $_reference): void
    {
        $this->reference = $_reference;
    }

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @param int $_eventId
     */
    public function setEventId(int $_eventId): void
    {
        $this->eventId = $_eventId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEventDate(): DateTimeImmutable
    {
        return $this->eventDate;
    }

    /**
     * @param DateTimeImmutable $_eventDate
     */
    public function setEventDate(DateTimeImmutable $_eventDate): void
    {
        $this->eventDate = $_eventDate;
    }

    /**
     * @return int
     */
    public function getAttendees(): int
    {
        return $this->attendees;
    }

    /**
     * @param int $_attendees
     */
    public function setAttendees(int $_attendees): void
    {
        $this->attendees = $_attendees;
    }
}
