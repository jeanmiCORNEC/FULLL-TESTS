<?php

declare(strict_types=1);

namespace Fulll\UI\CLI;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\App\CreateFleetCommand as AppCreateFleetCommand;
use Fulll\App\CreateFleetHandler;
use Fulll\Infra\PostgresFleetRepository;
use Fulll\Domain\FleetRepositoryInterface;

#[AsCommand(
    name: 'create',
    description: 'Creates a new fleet for a given user.'
)]
class CreateFleetCommand extends Command
{
    // Inject the dependency via the constructor
    public function __construct(private FleetRepositoryInterface $fleetRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('userId', InputArgument::REQUIRED, 'The ID of the user creating the fleet.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {



        // 1. Get the user ID from the command line argument.
        $userId = $input->getArgument('userId');

        // 2. Create the application command (DTO).
        $command = new AppCreateFleetCommand($userId);

        // 3. Instantiate and call the handler.
        $handler = new CreateFleetHandler($this->fleetRepository);
        $fleetId = $handler->handle($command);

        // 4. Output the result to the user.
        $output->writeln('<info>Fleet created with ID: ' . $fleetId . '</info>');

        return Command::SUCCESS;
    }
}
