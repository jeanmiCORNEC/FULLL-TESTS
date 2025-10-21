<?php

declare(strict_types=1);

namespace Fulll\App;

/**
 * DTO for the intention of parking a vehicle.
 */
class ParkVehicleCommand
{
    public function __construct(
        private string $fleetId,
        private string $vehiclePlateNumber,
        private float $latitude,
        private float $longitude
    ) {}

    public function getFleetId(): string
    {
        return $this->fleetId;
    }

    public function getVehiclePlateNumber(): string
    {
        return $this->vehiclePlateNumber;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}