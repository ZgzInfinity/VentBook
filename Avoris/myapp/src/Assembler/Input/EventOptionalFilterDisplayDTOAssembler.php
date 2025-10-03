<?php

namespace App\Assembler\Input;

use App\DTO\Input\EventOptionalFilterDisplayDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Input assembler for searching event filters
 */
class EventOptionalFilterDisplayDTOAssembler
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
     * Transforms input parameters into an EventOptionalFilterDisplayDTO and validates it.
     *
     * @param array $params Input parameters, expected to contain 'filters'.
     * @return EventOptionalFilterDisplayDTO Validated DTO object.
     * @throws ValidationFailedException If validation fails.
     */
    public function transform(array $params): EventOptionalFilterDisplayDTO
    {
        $filters = $params['filters'] ?? [];

        $event = new EventOptionalFilterDisplayDTO();
        $event->setFilters($filters);

        $violations = $this->validator->validate($event);

        if (count($violations) > 0) {
            throw new ValidationFailedException($event, $violations);
        }
        
        return $event;
    }
}
