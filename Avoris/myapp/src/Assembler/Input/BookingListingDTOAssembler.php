<?php

namespace App\Assembler\Input;

use App\DTO\Input\BookingListingDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Input assembler to list bookings of user by ID
 */
class BookingListingDTOAssembler
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator Symfony validator service.
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Transforms input parameters into a validated BookingListingDTO object.
     *
     * @param array $params Input parameters with key 'identification'.
     * @return BookingListingDTO The validated DTO instance.
     * @throws ValidationFailedException When validation fails.
     */
    public function transform(array $params): BookingListingDTO
    {
        $booking = new BookingListingDTO();
        $booking->setIdentification($params['id']);

        $violations = $this->validator->validate($booking);

        if (count($violations) > 0) {
            throw new ValidationFailedException($booking, $violations);
        }
        
        return $booking;
    }
}
