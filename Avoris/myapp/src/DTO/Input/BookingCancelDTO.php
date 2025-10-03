<?php

namespace App\DTO\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO used to represent and validate the input needed to cancel a booking.
 */
class BookingCancelDTO
{
    /**
     * @var string ID of the booking to cancel
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    private string $id;

    /**
     * Get the ID of the booking.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the ID of the booking.
     *
     * @param string $_id
     * @return void
     */
    public function setId(string $_id): void
    {
        $this->id = $_id;
    }
}
