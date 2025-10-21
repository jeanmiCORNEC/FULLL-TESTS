<?php

declare(strict_types=1);

namespace Fulll\UI\CLI;

use Fulll\App\RegisterVehicleCommand;
use Fulll\App\RegisterVehicleHandler;
use Fulll\Domain\FleetRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'register-vehicle',
    description: 'Registers a vehicle into a given fleet.'
)]
class RegisterVehicleCliCommand extends Command
{
    public function __construct(private FleetRepositoryInterface $fleetRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fleetId', InputArgument::REQUIRED, 'The ID of the fleet')
            ->addArgument('vehiclePlateNumber', InputArgument::REQUIRED, 'The plate number of the vehicle to register');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $command = new RegisterVehicleCommand(
                $input->getArgument('fleetId'),
                $input->getArgument('vehiclePlateNumber')
            );

            $handler = new RegisterVehicleHandler($this->fleetRepository);
            $handler->handle($command);

            $output->writeln('<info>Vehicle registered successfully!</info>');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
