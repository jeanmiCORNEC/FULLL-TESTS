<?php

declare(strict_types=1);

namespace Fulll\Infra;

use Fulll\Domain\Fleet;

/**
 * In-memory implementation of a fleet repository for testing purposes.
 */
class InMemoryFleetRepository
{
    /** @var Fleet[] */
    private array $fleets = [];

    /**
     * Saves a fleet to the repository.
     */
    public function save(Fleet $fleet): void
    {
        $this->fleets[$fleet->getId()] = $fleet;
    }

    /**
     * Finds a fleet by its ID.
     */
    public function findById(string $fleetId): ?Fleet
    {
        return $this->fleets[$fleetId] ?? null;
    }
}