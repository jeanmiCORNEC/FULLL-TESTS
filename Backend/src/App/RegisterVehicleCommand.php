<?php

declare(strict_types=1);

namespace Fulll\App;

/**
 * Represents the intention to register a vehicle into a fleet.
 * This is a Data Transfer Object (DTO) carrying the necessary information.
 */
class RegisterVehicleCommand
{
    public function __construct(
        private string $fleetId,
        private string $vehiclePlateNumber
    ) {}

    public function getFleetId(): string
    {
        return $this->fleetId;
    }

    public function getVehiclePlateNumber(): string
    {
        return $this->vehiclePlateNumber;
    }
}