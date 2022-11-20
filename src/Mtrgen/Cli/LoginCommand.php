<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Registry\Profile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class LoginCommand extends Command
{
    protected static $defaultName = 'login';
    protected static $defaultDescription = 'Login to the online registry.';

    public function configure(): void
    {
        $this->setAliases(['in']);
        $this->addArgument('username', InputArgument::REQUIRED, 'Username.');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password.');
        $this->addOption('duration', 'd', InputOption::VALUE_REQUIRED, 'The duration (in hours) for which the user should stay logged in. Use 0 to never logout - not recommended! (default=24)');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username') ?? null;
        $password = $input->getArgument('password') ?? null;
        $duration = (int) ($input->getOption('duration') ?? 24);

        /** @var SymfonyQuestionHelper $helper */
        $helper = $this->getHelper('question');
        if (!$username) {
            $io->newLine();
            $usernameQuestion = new Question('<comment><options=bold>Enter your username</>:</comment> ');
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
            $passwordQuestion = new Question('<comment><options=bold>Enter your password</>:</comment> ');
            $password = $helper->ask($input, $output, $passwordQuestion);
            $io->newLine();
        }

        $connection = new Connection;
        $response = $connection->login($username, $password, $duration);

        if (!isset($response->status) || $response->status !== 'success' || !isset($response->token)) {
            $io->error($response->message ?? 'Something went wrong. Try again.');
            return self::FAILURE;
        }

        $profile = new Profile();
        $profile->writeToProfile($username, $response->token);

        $io->text("<fg=green>Logged in as $username.</>");
        $io->newLine();
        return self::SUCCESS;
    }
}
