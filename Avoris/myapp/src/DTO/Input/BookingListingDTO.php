<?php

namespace App\DTO\Input;

use App\Traits\IdentificationTrait;

/**
 * DTO used to list all bookings of a user based on their identification (DNI).
 *
 * This class only exposes the `identification` field through the IdentificationTrait.
 */
class BookingListingDTO
{
    use IdentificationTrait;
}
