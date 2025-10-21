<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use Fulll\App\RegisterVehicleCommand;
use Fulll\App\RegisterVehicleHandler;
use Fulll\Domain\Vehicle;
use Fulll\Domain\Fleet;
use Fulll\Domain\Location;
use Fulll\Infra\InMemoryFleetRepository;
use Fulll\App\ParkVehicleCommand;
use Fulll\App\ParkVehicleHandler;

class FeatureContext implements Context
{
    /** @var Fleet The main fleet for the current scenario. */
    private $fleet;

    /** @var Vehicle The main vehicle for the current scenario. */
    private $vehicle;

    /** @var \Exception|null The exception caught during the scenario. */
    private ?\Exception $caughtException = null;

    /** @var InMemoryFleetRepository Repository to manage fleets in memory. */
    private InMemoryFleetRepository $fleetRepository;

    /** @var Fleet The fleet belonging to another user. */
    private Fleet $anotherFleet;

    /** @var \Fulll\Domain\Location The location for the current scenario. */
    private \Fulll\Domain\Location $location;

    public function __construct()
    {
        // Each scenario has its own empty repository
        $this->fleetRepository = new InMemoryFleetRepository();
    }

    #[Given('my fleet')]
    public function myFleet(): void
    {
        $this->fleet = Fleet::createForUser('user-1');
        $this->fleetRepository->save($this->fleet);
    }

    #[Given('a vehicle')]
    public function aVehicle(): void
    {
        $this->vehicle = new Vehicle('X-WING-99');
    }

    #[When('I register this vehicle into my fleet')]
    public function iRegisterThisVehicleIntoMyFleet(): void
    {
        // Call the private helper method to perform the registration.
        $this->registerTheVehicle();
    }

    #[Then('this vehicle should be part of my vehicle fleet')]
    public function thisVehicleShouldBePartOfMyVehicleFleet(): void
    {
        // Retrieve the fleet from the repository to verify the vehicle registration.
        $fleetFromRepository = $this->fleetRepository->findById($this->fleet->getId());

        if (!$fleetFromRepository->hasVehicle($this->vehicle)) {
            throw new \Exception('Vehicle was not found in the fleet.');
        }
    }

    #[Given('I have registered this vehicle into my fleet')]
    public function iHaveRegisteredThisVehicleIntoMyFleet(): void
    {
        // Call the private helper method to perform the registration.
        $this->registerTheVehicle();
    }

    #[When('I try to register this vehicle into my fleet')]
    public function iTryToRegisterThisVehicleIntoMyFleet(): void
    {
        try {
            $this->registerTheVehicle();
        } catch (\Exception $e) {
            // We expect an exception. We catch it and store it for the "Then" step.
            $this->caughtException = $e;
        }
    }

    #[Then('I should be informed this this vehicle has already been registered into my fleet')]
    public function iShouldBeInformedThisThisVehicleHasAlreadyBeenRegisteredIntoMyFleet(): void
    {
        // 1. First, we check if an exception was caught at all.
        // If not, the test should fail because we expected an error.
        if ($this->caughtException === null) {
            throw new \RuntimeException('Expected an exception to be thrown, but it was not.');
        }
        // 2. Second, we can check if the exception message is the one we expect.
        // This makes the test more robust.
        $expectedMessage = 'This vehicle has already been registered into this fleet';
        if ($this->caughtException->getMessage() !== $expectedMessage) {
            throw new \RuntimeException(
                sprintf(
                    "The exception message is incorrect. Expected '%s', got '%s'.",
                    $expectedMessage,
                    $this->caughtException->getMessage()
                )
            );
        }
    }

    #[Given('the fleet of another user')]
    public function theFleetOfAnotherUser(): void
    {
        // We create a new Fleet instance, just like for the main user
        $this->anotherFleet = Fleet::createForUser('user-2');

        // And we save it to the repository so the handler can find it
        $this->fleetRepository->save($this->anotherFleet);
    }

    #[Given('this vehicle has been registered into the other user\'s fleet')]
    public function thisVehicleHasBeenRegisteredIntoTheOtherUsersFleet(): void
    {
        // We use the same CQRS pattern, but targeting the other user's fleet.
        $command = new RegisterVehicleCommand(
            $this->anotherFleet->getId(),
            $this->vehicle->getPlateNumber()
        );

        $handler = new RegisterVehicleHandler($this->fleetRepository);

        $handler->handle($command);
    }

    #[Given('a location')]
    public function aLocation(): void
    {
        // For the test, we create a location with sample GPS coordinates.
        $this->location = new Location(43.2839533, 5.3712377); // Coordinates for Notre Dame de la Garde, Marseille
    }

    #[When('I park my vehicle at this location')]
    public function iParkMyVehicleAtThisLocation(): void
    {
        // Call the private helper method to perform the parking action.
        $this->parkTheVehicle();
    }

    #[Then('the known location of my vehicle should verify this location')]
    public function theKnownLocationOfMyVehicleShouldVerifyThisLocation(): void
    {
        // 1. To be sure, we retrieve the fleet's state from our persistence layer.
        $fleetFromRepository = $this->fleetRepository->findById($this->fleet->getId());

        // 2. We ask the fleet for the vehicle's last known location.
        $knownLocation = $fleetFromRepository->getVehicleLocation($this->vehicle->getPlateNumber());

        // 3. We verify that a location was actually found.
        if ($knownLocation === null) {
            throw new \RuntimeException('The vehicle location could not be found in the fleet.');
        }

        // 4. We assert that the known location's coordinates match the location from our scenario.
        if (
            $knownLocation->getLatitude() !== $this->location->getLatitude() ||
            $knownLocation->getLongitude() !== $this->location->getLongitude()
        ) {
            throw new \RuntimeException(
                sprintf(
                    'The vehicle is at the wrong location. Expected lat %s, lon %s but got lat %s, lon %s.',
                    $this->location->getLatitude(),
                    $this->location->getLongitude(),
                    $knownLocation->getLatitude(),
                    $knownLocation->getLongitude()
                )
            );
        }
    }

    #[Given('my vehicle has been parked into this location')]
    public function myVehicleHasBeenParkedIntoThisLocation(): void
    {
        // Call the private helper method to perform the parking action.
        $this->parkTheVehicle();
    }

    #[When('I try to park my vehicle at this location')]
    public function iTryToParkMyVehicleAtThisLocation(): void
    {
        try {
            // We use our helper method to attempt the action
            $this->parkTheVehicle();
        } catch (\Exception $e) {
            // We expect an exception, so we catch it and store it for verification.
            $this->caughtException = $e;
        }
    }

    #[Then('I should be informed that my vehicle is already parked at this location')]
    public function iShouldBeInformedThatMyVehicleIsAlreadyParkedAtThisLocation(): void
    {
        // 1. Assert that an exception was caught.
        if ($this->caughtException === null) {
            throw new \RuntimeException('Expected an exception to be thrown, but it was not.');
        }

        // 2. Assert that the exception message is the one we expect.
        $expectedMessage = 'This vehicle is already parked at this location.';
        if ($this->caughtException->getMessage() !== $expectedMessage) {
            throw new \RuntimeException(
                sprintf(
                    "The exception message is incorrect. Expected '%s', got '%s'.",
                    $expectedMessage,
                    $this->caughtException->getMessage()
                )
            );
        }
    }

    // Private helper methods

    /**
     * Helper method to perform the vehicle registration logic.
     * Avoids code duplication between steps.
     */
    private function registerTheVehicle(): void
    {
        $command = new RegisterVehicleCommand(
            $this->fleet->getId(),
            $this->vehicle->getPlateNumber()
        );

        $handler = new RegisterVehicleHandler($this->fleetRepository);

        $handler->handle($command);
    }

    /**
     * Helper method to perform the vehicle parking logic.
     * Avoids code duplication between steps.
     */
    private function parkTheVehicle(): void
    {
        $command = new ParkVehicleCommand(
            $this->fleet->getId(),
            $this->vehicle->getPlateNumber(),
            $this->location->getLatitude(),
            $this->location->getLongitude()
        );

        $handler = new ParkVehicleHandler($this->fleetRepository);

        $handler->handle($command);
    }
}
