<?php

namespace App\Service;

use App\Assembler\Input\BookingListingDTOAssembler as InputDTOAssembler;
use App\Assembler\Output\BookingInfoDataDTOAssembler as OutputDTOAssembler;
use App\Repository\BookingRepository;

/**
 * Service responsible for fetching bookings filtered by user identification.
 */
class BookingListingService
{
    /**
     * @var BookingRepository
     */
    private BookingRepository $bookingRepository;

    /**
     * @var InputDTOAssembler
     */
    private InputDTOAssembler $inputDTOAssembler;

    /**
     * @var OutputDTOAssembler
     */
    private OutputDTOAssembler $outputDTOAssembler;

    /**
     * @param BookingRepository  $bookingRepository
     * @param InputDTOAssembler  $inputDTOAssembler
     * @param OutputDTOAssembler $outputDTOAssembler
     */
    public function __construct(
        BookingRepository $bookingRepository,
        InputDTOAssembler $inputDTOAssembler,
        OutputDTOAssembler $outputDTOAssembler
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->inputDTOAssembler = $inputDTOAssembler;
        $this->outputDTOAssembler = $outputDTOAssembler;
    }

    /**
     * Executes the service fetching bookings for a given identification.
     *
     * @param array
     * @return array|null 
     */
    public function execute(array $params): ?array
    {
        $dto = $this->inputDTOAssembler->transform($params);
        $bookings = $this->bookingRepository->findByIdentification($dto->getIdentification());

        if (empty($bookings)) {
            return null;
        }

        $bookingsArray = [];
        foreach ($bookings as $booking) {
            $bookingsArray[] = $this->outputDTOAssembler->transform($booking);
        }

        return $bookingsArray;
    }
}
