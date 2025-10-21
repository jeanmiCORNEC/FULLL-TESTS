<?php

declare(strict_types=1);

namespace Fulll\App;

use Fulll\Infra\InMemoryFleetRepository;
use Fulll\Domain\Vehicle;

/**
 * Handles the RegisterVehicleCommand to perform the registration action.
 */
class RegisterVehicleHandler
{
    /** @var InMemoryFleetRepository Repository to manage fleets.*/
     private InMemoryFleetRepository $fleetRepository;

    public function __construct(InMemoryFleetRepository $fleetRepository)
    {
        $this->fleetRepository = $fleetRepository;
    }

    /**
     * Executes the command.
     */
    public function handle(RegisterVehicleCommand $command): void
    {
        // Retrieve the fleet from the repository.
        $fleet = $this->fleetRepository->findById($command->getFleetId());
        if ($fleet === null) {
            throw new \RuntimeException('Fleet not found');
        }

        // Create a Vehicle instance from the command data.
        $vehicle = new Vehicle($command->getVehiclePlateNumber());

        // Register the vehicle into the fleet.
        $fleet->registerVehicle($vehicle);

        // Save the updated fleet back to the repository.
        $this->fleetRepository->save($fleet);
    }
}