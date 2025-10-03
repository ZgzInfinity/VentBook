<?php

namespace App\Service;

use App\Assembler\Input\EventDisplayDTOAssembler as InputDTOAssembler;
use App\Assembler\Output\EventInfoDataDTOAssembler as OutputDTOAssembler;
use App\DTO\Output\EventInfoDataDTO;
use App\Repository\EventRepository;


/**
 * Service responsible for showing extended info event
 */
class EventDisplayService
{
    /**
     * @var EventRepository
     */
    private EventRepository $eventRepository;

    /**
     * @var InputDTOAssembler
     */
    private InputDTOAssembler $inputDTOAssembler;

    /**
     * @var OutputDTOAssembler
     */
    private OutputDTOAssembler $outputDTOAssembler;

    /**
     * @param EventRepository $eventRepository
     * @param InputDTOAssembler $inputDTOAssembler
     * @param OutputDTOAssembler $outputDTOAssembler
     */
    public function __construct(
        EventRepository $eventRepository,
        InputDTOAssembler $inputDTOAssembler,
        OutputDTOAssembler $outputDTOAssembler
    ) {
        $this->eventRepository = $eventRepository;
        $this->inputDTOAssembler = $inputDTOAssembler;
        $this->outputDTOAssembler = $outputDTOAssembler;
    }

    /**
     * Returns th information data of an event
     * @param array $params
     * @return EventInfoDataDTO|null
     */
    public function execute(array $params): ?EventInfoDataDTO
    {
        $dto = $this->inputDTOAssembler->transform($params);
        $event = $this->eventRepository->findById($dto->getId());

        if (!$event) {
            return null;
        }

        return $this->outputDTOAssembler->transform($event);
    }
}
