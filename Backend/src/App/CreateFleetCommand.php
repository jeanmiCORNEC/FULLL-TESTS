<?php

declare(strict_types=1);

namespace Fulll\App;

/**
 * DTO for the intention of creating a new fleet.
 */
class CreateFleetCommand
{
    public function __construct(private string $userId)
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
