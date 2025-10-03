<?php

namespace App\Assembler\Input;

use App\DTO\Input\BookingCancelDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Transforms raw input data into a BookingCancelDTO
 * and validates it using the Symfony Validator component.
 */
class BookingCancelDTOAssembler
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Create and validate a BookingCancelDTO from input array.
     *
     * @param array $params  Input parameters (must contain “id”)
     * @return BookingCancelDTO
     * @throws ValidationFailedException
     */
    public function transform(array $params): BookingCancelDTO
    {
        $booking = new BookingCancelDTO();
        $booking->setId($params['id']);

        $violations = $this->validator->validate($booking);

        if (count($violations) > 0) {
            throw new ValidationFailedException($booking, $violations);
        }

        return $booking;
    }
}
