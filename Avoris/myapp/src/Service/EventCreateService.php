<?php

namespace App\Service;

use App\Assembler\Input\EventCreateDTOAssembler;
use App\Repository\EventRepository;

/**
 * Service responsible for creating events.
 */
class EventCreateService
{
    /**
     * @var EventCreateDTOAssembler
     */
    private EventCreateDTOAssembler $assembler;

    /**
     * @var EventRepository
     */
    private EventRepository $eventRepository;

    /**
     * @param EventCreateDTOAssembler $assembler
     * @param EventRepository         $eventRepository
     */
    public function __construct(
        EventCreateDTOAssembler $assembler,
        EventRepository $eventRepository
    ) {
        $this->assembler = $assembler;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Executes the creation of an event.
     * @param array $params
     * @return int
     */
    public function execute(array $params): int
    {
        $dto = $this->assembler->transform($params);

        return $this->eventRepository->insert($dto);
    }
}
