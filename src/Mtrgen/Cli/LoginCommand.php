<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Store\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class LoginCommand extends Command
{
    protected static $defaultName = 'login';
    protected static $defaultDescription = 'Login to the online registry.';

    public function configure(): void
    {
        $this->setAliases(['l']);
        $this->addArgument('username', InputArgument::REQUIRED, 'Username.');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username') ?? null;
        $password = $input->getArgument('password') ?? null;

        if (!$username || !$password) {
            $io->error("Username or password missing.");
            return self::FAILURE;
        }

        $connection = new Connection;

        $connection->writeToProfile($username, $password);

        return self::SUCCESS;
    }
}
