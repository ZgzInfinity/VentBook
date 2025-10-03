<?php

namespace App\DTO\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for optional filters used in event display.
 */
class EventOptionalFilterDisplayDTO
{
    /**
     * Optional filters array.
     *
     * @var array|null
     */
    #[Assert\Type('array')]
    private ?array $filters = null;

    /**
     * Gets the optional filters.
     *
     * @return array|null
     */
    public function getFilters(): ?array
    {
        return $this->filters;
    }

    /**
     * Sets the optional filters.
     *
     * @param array|null $_filters
     * @return void
     */
    public function setFilters(?array $_filters): void
    {
        $this->filters = $_filters;
    }
}
