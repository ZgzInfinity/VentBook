<?php

namespace App\Assembler\Input;

use App\DTO\Input\EventCreateDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use DateTimeImmutable;

/**
 * Input assembler to create an event
 */
class EventCreateDTOAssembler
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
     * Transforms input array parameters into a validated EventCreateDTO object.
     *
     * @param array $params Input parameters with keys: name, description, from_date, to_date, available_seats.
     * @return EventCreateDTO The validated DTO instance.
     * @throws ValidationFailedException When validation fails.
     */
    public function transform(array $params): EventCreateDTO
    {
        $event = new EventCreateDTO();
        $event->setName($params['name']);
        $event->setDescription($params['description']);
        $event->setFromDate(new DateTimeImmutable($params['from_date']));
        $event->setToDate(new DateTimeImmutable($params['to_date']));
        $event->setAvailableSeats($params['available_seats']);

        $violations = $this->validator->validate($event);

        if (count($violations) > 0) {
            throw new ValidationFailedException($event, $violations);
        }

        return $event;
    }
}
