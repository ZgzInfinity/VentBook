<?php

namespace App\Domain;

use InvalidArgumentException;

class Booking
{

    private const DNI_FORMAT = '/^\d{8}[A-Za-z]$/';
    private const MINIMUM_LENGTH = 12;
    private const MAXIMUN_LENGTH = 16;

    public function __construct(
        private readonly string $reference,
        private readonly int $eventId,
        private readonly \DateTimeImmutable $eventDate,
        private readonly int $attendees,
        private readonly string $buyerId
    ) {

        $length = strlen($buyerId);
        if ($length < self::MINIMUM_LENGTH || $length > self::MAXIMUN_LENGTH) {
            throw new InvalidArgumentException(
                "Buyer ID (DNI) length must be between 12 and 16 characters."
            );
        }

        if (!preg_match(self::DNI_FORMAT, $this->buyerId)) {
            throw new InvalidArgumentException('Invalid DNI format.');
        }

        if ($this->attendees <= 0) {
            throw new InvalidArgumentException('Attendees must be greater than 0.');
        }
    }

    public function reference(): string {
        return $this->reference;
    }

    public function eventId(): int {
        return $this->eventId;
    }

    public function eventDate(): \DateTimeImmutable {
        return $this->eventDate;
    }

    public function attendees(): int {
        return $this->attendees;
    }

    public function buyerId(): string {
        return $this->buyerId;
    }
}
