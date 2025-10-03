<?php

namespace App\Assembler\Output;

use App\DTO\Output\BookingInfoDataDTO;
use DateTimeImmutable;

/**
 * Output assembler to represent data of an booking
 */
class BookingInfoDataDTOAssembler
{
    /**
     * Transforms an array of booking data into a BookingInfoDataDTO.
     *
     * @param array $params Associative array with booking data.
     * @return BookingInfoDataDTO The populated DTO object.
     */
    public function transform(array $params): BookingInfoDataDTO
    {
        $booking = new BookingInfoDataDTO();
        $booking->setReference($params['reference']);
        $booking->setEventId($params['event_id']);
        $booking->setEventDate(new DateTimeImmutable($params['event_date']));
        $booking->setAttendees($params['attendees']);
        $booking->setIdentification($params['buyer_id']);

        return $booking;
    }
}
