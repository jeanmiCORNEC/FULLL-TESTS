<?php

declare(strict_types=1);

namespace Fulll\UI\CLI;

use Fulll\App\ParkVehicleCommand;
use Fulll\App\ParkVehicleHandler;
use Fulll\Domain\FleetRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'localize-vehicle',
    description: 'Parks a vehicle at a specific location.'
)]
class LocalizeVehicleCliCommand extends Command
{
    public function __construct(private FleetRepositoryInterface $fleetRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fleetId', InputArgument::REQUIRED, 'The ID of the fleet')
            ->addArgument('vehiclePlateNumber', InputArgument::REQUIRED, 'The plate number of the vehicle')
            ->addArgument('lat', InputArgument::REQUIRED, 'The latitude of the location')
            ->addArgument('lng', InputArgument::REQUIRED, 'The longitude of the location');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $command = new ParkVehicleCommand(
                $input->getArgument('fleetId'),
                $input->getArgument('vehiclePlateNumber'),
                (float) $input->getArgument('lat'), // Cast to float for type safety
                (float) $input->getArgument('lng')  // Cast to float for type safety
            );

            $handler = new ParkVehicleHandler($this->fleetRepository);
            $handler->handle($command);

            $output->writeln('<info>Vehicle location has been updated successfully!</info>');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
