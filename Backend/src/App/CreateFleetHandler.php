<?php

declare(strict_types=1);

namespace Fulll\App;

use Fulll\Domain\Fleet;
use Fulll\Infra\InMemoryFleetRepository;
use Fulll\Domain\FleetRepositoryInterface;

/**
 * Handles the command to create a fleet.
 */
class CreateFleetHandler
{
    public function __construct(private FleetRepositoryInterface $fleetRepository)
    {
    }

    /**
     * Executes the command.
     *
     * @return string The ID of the newly created fleet.
     */
    public function handle(CreateFleetCommand $command): string
    {
        // For now, the user ID is not stored, but in a real app it would be.
        $fleet = Fleet::createForUser($command->getUserId());
        
        $this->fleetRepository->save($fleet);

        return $fleet->getId();
    }
}