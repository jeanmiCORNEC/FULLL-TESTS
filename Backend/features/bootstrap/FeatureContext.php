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
use Fulll\Infra\InMemoryFleetRepository;

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

    public function __construct()
    {
        // Each scenario has its own empty repository
        $this->fleetRepository = new InMemoryFleetRepository();
    }

    #[Given('my fleet')]
    public function myFleet(): void
    {
        $this->fleet = new \Fulll\Domain\Fleet();
        $this->fleetRepository->save($this->fleet);
    }

    #[Given('a vehicle')]
    public function aVehicle(): void
    {
        $this->vehicle = new \Fulll\Domain\Vehicle('X-WING-99');
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
    $this->anotherFleet = new Fleet();
    
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
}
