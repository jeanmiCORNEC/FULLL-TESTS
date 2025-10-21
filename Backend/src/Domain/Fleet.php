<?php

declare(strict_types=1);

namespace Fulll\Domain;

class Fleet
{
    /** @var string The unique identifier for this fleet. */
    private string $id;

    /** @var Vehicle[] */
    private array $vehicles = [];

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
}