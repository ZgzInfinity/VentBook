<?php

namespace App\Assembler\Input;

use App\DTO\Input\EventDisplayDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Input assembler to show detail info of and event
 */
class EventDisplayDTOAssembler
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
     * Transforms input parameters into an EventDisplayDTO and validates it.
     *
     * @param array $params Input parameters, expected to contain 'id'.
     * @return EventDisplayDTO Validated DTO object.
     * @throws ValidationFailedException If validation fails.
     */
    public function transform(array $params): EventDisplayDTO
    {
        $event = new EventDisplayDTO();
        $event->setId($params['id']);

        $violations = $this->validator->validate($event);

        if (count($violations) > 0) {
            throw new ValidationFailedException($event, $violations);
        }

        return $event;
    }
}
