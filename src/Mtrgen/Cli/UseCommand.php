<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\FileGenerator;
use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\Generator;
use Matronator\Parsem\Parser;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class UseCommand extends Command
{
    private const CUSTOM_TEMPLATE_ANSWER = 'Custom template (enter path to the file)';

    protected static $defaultName = 'use';
    protected static $defaultDescription = 'Generates a file using a template from the online registry.';

    public function configure(): void
    {
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'The identifier of the template to use from the online registry.');
        $this->addArgument('args', InputArgument::IS_ARRAY, 'Arguments to pass to the template (\'key=value\' items seperated by space).');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $identifier = $input->getArgument('identifier') ?? null;
        if (!$identifier) {
            $io->newLine();
            $nameQuestion = new Question('<comment><options=bold>Enter the template identifier (vendor/name)</>:</comment> ');
            $validateName = Validation::createCallable(new Regex([
                'pattern' => '/^[a-zA-Z0-9_-]+?\/[a-zA-Z0-9_-]+?$/',
                'message' => 'Name must be in format "vendor/name".',
            ]));
            $nameQuestion->setValidator($validateName);
            $identifier = $helper->ask($input, $output, $nameQuestion);
            $io->newLine();
        }

        $connection = new Connection;
        $arguments = $this->getArguments($input->getArgument('args')) ?? null;
        if (!$arguments) {
            $template = $connection->getTemplate($identifier);
            $contents = $template->contents;
            if (!$template || !$contents) {
                $io->error('Template not found.');
                return Command::FAILURE;
            }

            $output->writeln('<fg=green>Template found!</>');
            $output->writeln('Looking for template parameters...');

            $args = Parser::getArguments($contents);
            if ($args !== []) {
                $io->writeln('<fg=green>Template parameters found!</>');
                $io->newLine();
                $arguments = [];
                foreach ($args as $arg) {
                    $argQuestion = new Question("<comment><options=bold>Enter the value for parameter '$arg'</>:</comment> ");
                    $arguments[$arg] = $helper->ask($input, $output, $argQuestion);
                    $io->newLine();
                }
            }
        }

        $output->writeln("Generating file from template <options=bold>$identifier</>...");
        $io->newLine();

        FileGenerator::writeFile(Generator::parse($template->filename, $contents, $arguments));

        $output->writeln('<fg=green>Done!</>');
        $io->newLine();

        return self::SUCCESS;
    }

    private function getArguments(array $args): array
    {
        $arguments = [];
        foreach ($args as $arg) {
            $exploded = explode('=', $arg);
            $arguments[$exploded[0]] = $exploded[1];
        }

        return $arguments;
    }
}
