<?php

namespace App\Domain;

use InvalidArgumentException;

class Event
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $description,
        private readonly \DateTimeImmutable $fromDate,
        private readonly \DateTimeImmutable $toDate,
        private int $availableSeats
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Event name cannot be empty.');
        }

        if (empty($this->description)) {
            throw new InvalidArgumentException('Event description cannot be empty.');
        }

        if ($this->fromDate > $this->toDate) {
            throw new InvalidArgumentException('Event start date must be before end date.');
        }

        if ($this->availableSeats < 0) {
            throw new InvalidArgumentException('Available seats cannot be negative.');
        }
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getFromDate(): \DateTimeImmutable {
        return $this->fromDate;
    }

    public function getToDate(): \DateTimeImmutable {
        return $this->toDate;
    }

    public function getAvailableSeats(): int {
        return $this->availableSeats;
    }

    public function reduceSeats(int $qty): void
    {
        if ($qty > $this->availableSeats) {
            throw new InvalidArgumentException('Not enough seats available.');
        }

        $this->availableSeats -= $qty;
    }

    public function increaseSeats(int $qty): void
    {
        $this->availableSeats += $qty;
    }
}
