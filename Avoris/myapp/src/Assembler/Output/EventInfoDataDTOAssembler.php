<?php

namespace App\Assembler\Output;

use App\DTO\Output\EventInfoDataDTO;
use DateTimeImmutable;

/**
 * Output assembler to represent data of an event
 */
class EventInfoDataDTOAssembler
{
    /**
     * Transforms an array of event data into an EventInfoDataDTO.
     *
     * @param array $params Associative array with event data.
     * @param bool $displayDescription Whether to include the description field.
     * @return EventInfoDataDTO The populated DTO object.
     */
    public function transform(array $params, bool $displayDescription = true): EventInfoDataDTO
    {
        $event = new EventInfoDataDTO();
        $event->setName($params['name']);
        $event->setFromDate(new DateTimeImmutable($params['from_date']));
        $event->setToDate(new DateTimeImmutable($params['to_date']));
        $event->setAvailableSeats($params['available_seats']);

        if ($displayDescription) {
            $event->setDescription($params['description']);
        }

        return $event;
    }
}
