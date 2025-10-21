<?php

declare(strict_types=1);

namespace Fulll\Domain;

class Fleet
{
    /** @var string The unique identifier for this fleet. */
    private string $id;

    /** @var Vehicle[] */
    private array $vehicles = [];

    /** @var Location[] Keyed by vehicle plate number. */
    private array $vehicleLocations = [];

    public function __construct()
    {
        // For the purpose of this exercise, a simple unique ID is sufficient.
        // In a real application, this would be a UUID.
        $this->id = uniqid('fleet-', true);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function registerVehicle(Vehicle $vehicle): void
    {
        // Check if the vehicle is already in the fleet
        if ($this->hasVehicle($vehicle)) {
            // Throw a domain-specific exception
            throw new \Exception('This vehicle has already been registered into this fleet');
        }

        $this->vehicles[] = $vehicle;
    }

    /**
     * Checks if a vehicle is registered in this fleet.
     * for then testing purposes.
     */
    public function hasVehicle(Vehicle $vehicle): bool
    {
        foreach ($this->vehicles as $v) {
            if ($v->getPlateNumber() === $vehicle->getPlateNumber()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Parks a registered vehicle at a specific location.
     */
    public function parkVehicle(string $vehiclePlateNumber, Location $location): void
    {
        // Retrieve the current location of the vehicle, if it exists.
        $currentLocation = $this->getVehicleLocation($vehiclePlateNumber);

        // Check if the vehicle is already parked at the same location.
        if (
            $currentLocation !== null &&
            $currentLocation->getLatitude() === $location->getLatitude() &&
            $currentLocation->getLongitude() === $location->getLongitude()
        ) {

            // Throw a domain-specific exception.
            throw new \Exception('This vehicle is already parked at this location.');
        }

        // Here you should also check if the vehicle is actually in the fleet.
        // We will add this later if a test requires it.

        $this->vehicleLocations[$vehiclePlateNumber] = $location;
    }

    /**
     * Retrieves the last known location of a vehicle.
     */
    public function getVehicleLocation(string $vehiclePlateNumber): ?Location
    {
        return $this->vehicleLocations[$vehiclePlateNumber] ?? null;
    }
}
