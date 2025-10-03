<?php

namespace App\Assembler\Input;

use App\DTO\Input\BookingCreateDTO;
use DateTimeImmutable;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Assembler to transform raw input data into a validated BookingCreateDTO.
 */
class BookingCreateDTOAssembler
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator Symfony validator component.
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Creates and validates a BookingCreateDTO from the given parameters.
     *
     * @param array $params Raw request parameters.
     * @return BookingCreateDTO Validated DTO.
     * @throws ValidationFailedException If any constraint violations occur.
     */
    public function transform(array $params): BookingCreateDTO
    {
        $booking = new BookingCreateDTO();
        $booking->setReference($params['reference']);
        $booking->setEventId($params['event_id']);
        $booking->setEventDate(new DateTimeImmutable($params['event_date']));
        $booking->setAttendees($params['attendees']);
        $booking->setIdentification($params['identification']);

        $violations = $this->validator->validate($booking);

        if (count($violations) > 0) {
            throw new ValidationFailedException($booking, $violations);
        }

        return $booking;
    }
}
