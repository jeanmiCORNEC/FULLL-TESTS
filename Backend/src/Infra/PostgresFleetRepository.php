<?php

declare(strict_types=1);

namespace Fulll\Infra;

use Fulll\Domain\Fleet;
use PDO;
use Fulll\Domain\FleetRepositoryInterface;
use Fulll\Domain\Vehicle;
use Fulll\Domain\Location;

class PostgresFleetRepository implements FleetRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        // Database connection details
        $host = 'localhost';
        $port = '5432';
        $db   = 'fulll_fleet';
        $user = 'user';
        $pass = 'password';

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";

        // Create a new PDO instance
        $this->pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function save(Fleet $fleet): void
    {
        // We wrap everything in a transaction to ensure data integrity.
        $this->pdo->beginTransaction();

        try {
            // Step 1: Save the fleet itself (UPSERT)
            $sqlFleet = "INSERT INTO fleets (id, user_id) VALUES (:id, :user_id)
                     ON CONFLICT (id) DO UPDATE SET user_id = :user_id";
            $stmtFleet = $this->pdo->prepare($sqlFleet);
            $stmtFleet->bindValue(':id', $fleet->getId());
            $stmtFleet->bindValue(':user_id', $fleet->getUserId());
            $stmtFleet->execute();

            // Step 2: Synchronize the vehicles.
            // First, we remove all existing vehicles for this fleet to handle deletions.
            $sqlDeleteVehicles = "DELETE FROM vehicles WHERE fleet_id = :fleet_id";
            $stmtDelete = $this->pdo->prepare($sqlDeleteVehicles);
            $stmtDelete->bindValue(':fleet_id', $fleet->getId());
            $stmtDelete->execute();

            // Second, we re-insert all vehicles currently in the Fleet object.
            $sqlInsertVehicle = "INSERT INTO vehicles (plate_number, fleet_id, lat, lng) 
                             VALUES (:plate_number, :fleet_id, :lat, :lng)";
            $stmtInsert = $this->pdo->prepare($sqlInsertVehicle);

            foreach ($fleet->getVehicles() as $vehicle) {
                $location = $fleet->getVehicleLocation($vehicle->getPlateNumber());

                $params = [
                    ':plate_number' => $vehicle->getPlateNumber(),
                    ':fleet_id' => $fleet->getId(),
                    ':lat' => $location?->getLatitude(),
                    ':lng' => $location?->getLongitude()
                ];

                $stmtInsert->bindValue(':plate_number', $params[':plate_number']);
                $stmtInsert->bindValue(':fleet_id', $params[':fleet_id']);
                $stmtInsert->bindValue(':lat', $params[':lat']);
                $stmtInsert->bindValue(':lng', $params[':lng']);
                $stmtInsert->execute();
            }


            // If everything went well, we commit the changes.
            $this->pdo->commit();
        } catch (\Exception $e) {
            // If anything fails, we roll back all changes.
            $this->pdo->rollBack();
            throw $e; // Re-throw the exception
        }
    }

    public function findById(string $fleetId): ?Fleet
    {
        // Step 1: Find the fleet itself
        $sqlFleet = "SELECT * FROM fleets WHERE id = :id";
        $stmtFleet = $this->pdo->prepare($sqlFleet);
        $stmtFleet->bindValue(':id', $fleetId);
        $stmtFleet->execute();
        $dataFleet = $stmtFleet->fetch(PDO::FETCH_ASSOC);

        if ($dataFleet === false) {
            return null;
        }

        // Step 2: Rehydrate the Fleet object from its basic state
        $fleet = Fleet::fromState($dataFleet['id'], $dataFleet['user_id']);

        // Step 3: Find and attach all associated vehicles
        $sqlVehicles = "SELECT * FROM vehicles WHERE fleet_id = :fleet_id";
        $stmtVehicles = $this->pdo->prepare($sqlVehicles);
        $stmtVehicles->bindValue(':fleet_id', $fleet->getId());
        $stmtVehicles->execute();

        while ($dataVehicle = $stmtVehicles->fetch(PDO::FETCH_ASSOC)) {
            $vehicle = new Vehicle($dataVehicle['plate_number']);

            // Use a new method to add a vehicle without re-checking business rules
            $fleet->addVehicle($vehicle);

            // If the vehicle has a location, rehydrate and park it
            if ($dataVehicle['lat'] !== null && $dataVehicle['lng'] !== null) {
                $location = new Location($dataVehicle['lat'], $dataVehicle['lng']);
                $fleet->parkVehicle($vehicle->getPlateNumber(), $location);
            }
        }

        return $fleet;
    }
}
