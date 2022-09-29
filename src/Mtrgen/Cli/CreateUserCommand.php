<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'signup';
    protected static $defaultDescription = 'Create new user account in the online registry.';

    public function configure(): void
    {
        $this->setAliases(['sign']);
        $this->addArgument('username', InputArgument::REQUIRED, 'Username.');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username') ?? null;
        $password = $input->getArgument('password') ?? null;

        $helper = $this->getHelper('question');
        if (!$username) {
            $io->newLine();
            $usernameQuestion = new Question('<comment><options=bold>Enter the username you want to register under</>:</comment> ');
            $validateName = Validation::createCallable(new Regex([
                'pattern' => '/^[a-zA-Z0-9_-]+?$/',
                'message' => 'Username can only contain letters, numbers, dash and underscore.',
            ]));
            $usernameQuestion->setValidator($validateName);
            $username = $helper->ask($input, $output, $usernameQuestion);
            $io->newLine();
        }

        if (!$password) {
            $io->newLine();
            $passwordQuestion = new Question('<comment><options=bold>Enter your password (min. 8 characters, at least 1 number and 1 uppercase and lowercase character)</>:</comment> ');
            $validatePassword = Validation::createCallable(new Regex([
                'pattern' => '/^(.{0,7}|[^0-9]*|[^A-Z]*|[^a-z]*)$/',
                'message' => 'Password must be at least 8 characters long and must contain at least 1 number, 1 lowercase and 1 uppercase character.',
            ]));
            $passwordQuestion->setValidator($validatePassword);
            $password = $helper->ask($input, $output, $passwordQuestion);
            $io->newLine();
        }

        $connection = new Connection;
        $response = $connection->createUser($username, $password);
        if ($response !== 'OK') {
            $io->error('Something went wrong.');
            return self::FAILURE;
        }

        $io->text("<options=bold;fg=green>User $username created.</> <fg=green>You may now login.</>");
        $io->newLine();
        return self::SUCCESS;
    }
}
