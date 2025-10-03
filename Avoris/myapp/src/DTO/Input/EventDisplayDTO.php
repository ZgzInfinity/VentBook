<?php

namespace App\DTO\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for displaying event information by ID.
 */
class EventDisplayDTO
{
    /**
     * The ID of the event.
     *
     * @var int
     */
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\PositiveOrZero]
    public int $id;

    /**
     * Gets the event ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the event ID.
     *
     * @param int $_id
     * @return void
     */
    public function setId(int $_id): void
    {
        $this->id = $_id;
    }
}
