<?php

namespace App\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait IdentificationTrait
 *
 * Provides a reusable "identification" property with validation rules
 * suitable for user identifiers (e.g. DNI), enforcing:
 *  - Non-empty value
 *  - Length between 12 and 16 characters
 *  - Alphanumeric characters only
 */
trait IdentificationTrait
{
    /**
     * Unique alphanumeric identifier of the user (DNI, etc.).
     *
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 12, max: 16)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9]+$/',
        message: 'The user identifier must be alphanumeric.'
    )]
    private string $identification;

    /**
     * Get identification value
     *
     * @return string
     */
    public function getIdentification(): string
    {
        return $this->identification;
    }

    /**
     * Set identification value
     *
     * @param string $identification
     * @return void
     */
    public function setIdentification(string $identification): void
    {
        $this->identification = $identification;
    }
}
