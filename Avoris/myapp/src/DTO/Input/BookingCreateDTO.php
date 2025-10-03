<?php

namespace App\DTO\Input;

use DateTimeImmutable;
use App\Traits\IdentificationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO used to validate the input when creating a new booking.
 */
class BookingCreateDTO
{
    use IdentificationTrait; // Contains buyer identification (DNI) with its own constraints

    /**
     * @var string Unique booking reference
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private string $reference;

    /**
     * @var int ID of the event to associate the booking with
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('integer')]
    private int $eventId;

    /**
     * @var DateTimeImmutable Date selected for the booking (must be within event date range)
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private DateTimeImmutable $eventDate;

    /**
     * @var int Number of attendees for the booking (must be > 1)
     */
    #[Assert\NotNull]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    #[Assert\GreaterThan(1)]
    private int $attendees;

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
