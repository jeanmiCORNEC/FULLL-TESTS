<?php

declare(strict_types=1);

namespace Fulll\App;

use Fulll\Infra\InMemoryFleetRepository;
use Fulll\Domain\Vehicle;
use Fulll\Domain\Location;
use Fulll\Domain\FleetRepositoryInterface;

/**
 * Handles the command to park a vehicle.
 */
class ParkVehicleHandler
{
    public function __construct(private FleetRepositoryInterface $fleetRepository)
    {
    }

    /**
     * Executes the command.
     */
    public function handle(ParkVehicleCommand $command): void
    {
        // 1. Retrieve the fleet
        $fleet = $this->fleetRepository->findById($command->getFleetId());
        if ($fleet === null) {
            throw new \RuntimeException('Fleet not found');
        }

        // 2. Create the Location Value Object
        $location = new Location(
            $command->getLatitude(),
            $command->getLongitude()
        );

        // 3. Ask the fleet to park the vehicle (this method doesn't exist yet)
        $fleet->parkVehicle($command->getVehiclePlateNumber(), $location);

        // 4. Save the changes
        $this->fleetRepository->save($fleet);
    }
}
