<?php

declare(strict_types=1);

namespace Fulll\Domain;

interface FleetRepositoryInterface
{
    public function save(Fleet $fleet): void;

    public function findById(string $fleetId): ?Fleet;
}
