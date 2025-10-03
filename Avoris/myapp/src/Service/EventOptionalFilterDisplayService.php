<?php

namespace App\Service;

use App\Repository\EventRepository;
use App\Assembler\Input\EventOptionalFilterDisplayDTOAssembler as InputDTOAssembler;
use App\Assembler\Output\EventInfoDataDTOAssembler as OutputDTOAssembler;

/**
 * Service responsible for display filtered events
 */
class EventOptionalFilterDisplayService
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

    private array $filterMapper = [
        'EVENT_FILTER_NAME' => 'name',
        'EVENT_FILTER_START_DATE' => 'from_date',
        'EVENT_FILTER_END_DATE' => 'to_date',
        'EVENT_FILTER_AVAILABLE' => 'available_seats'
    ];

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
     * Provides a list of events filtered or not
     * @param array $params
     * @throws InvalidArgumentException
     * @return array|null
     */
    public function execute(array $params): ?array
    {
        $dto = $this->inputDTOAssembler->transform($params);
        $filters = $dto->getFilters();
        $sqlConditions = [];
        $queryParams = [];

        if (!empty($filters)) {
            foreach ($filters as $filterItem) {
                if (is_array($filterItem)) {
                    foreach ($filterItem as $k => $v) {
                        $flattened[$k] = $v;
                    }
                }
            }
            $filters = $flattened;

            foreach ($filters as $filterKey => $filterValue) {

                if (!array_key_exists($filterKey, $this->filterMapper)) {
                    throw new \InvalidArgumentException(
                        "The filter " . $filterKey . " is not available."
                    );
                }

                if (empty($filterValue) || $filterValue === '') {
                    throw new \InvalidArgumentException(
                        "The filter " . $filterKey . " has not value."
                    );
                }

                $this->computeSearchingFilters(
                    $sqlConditions,
                    $queryParams,
                    $filterKey,
                    $filterValue
                );
            }
        }

        $events = $this->eventRepository->findFilteredEvents(
            $sqlConditions,
            $queryParams
        );

        if (!empty($events)) {
            $eventsArray = [];
            foreach ($events as $event) {
                $eventsArray[] = $this->outputDTOAssembler->transform($event, false);
            }
            return $eventsArray;
        }

        return null;
    }

    /**
     * Prepares the filters to fill the events to build the query
     * @param array &$sqlConditions
     * @param array &$queryParams
     * @param string $key
     * @param string $value
     * @return void
     */
    private function computeSearchingFilters(
        array &$sqlConditions,
        array &$queryParams,
        string $key,
        string $value
    ) {
        $column = $this->filterMapper[$key];

        switch ($key) {
            case 'EVENT_FILTER_NAME':
                $sqlConditions[] = "$column LIKE :$column";
                $queryParams[$column] = "%$value%";
                break;

            case 'EVENT_FILTER_START_DATE':
                $sqlConditions[] = "$column >= :$column";
                $queryParams[$column] = $value;
                break;

            case 'EVENT_FILTER_END_DATE':
                $sqlConditions[] = "$column <= :$column";
                $queryParams[$column] = $value;
                break;

            case 'EVENT_FILTER_AVAILABLE':
                $sqlConditions[] = "$column >= :$column";
                $queryParams[$column] = (int)$value;
                break;
        }
    }
}
