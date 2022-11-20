<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Store\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class AddTemplateCommand extends Command
{
    protected static $defaultName = 'add';
    protected static $defaultDescription = 'Get template from online registry and add it to the local store.';

    public function configure(): void
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'Full identifier of the template (vendor/name).');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $identifier = $input->getArgument('identifier') ?? null;

        /** @var SymfonyQuestionHelper $helper */
        $helper = $this->getHelper('question');
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
        $template = $connection->getTemplate($identifier);

        $storage = new Storage;
        if ($storage->download($identifier, $template->filename, $template->contents)) {
            $output->writeln("<fg=green>Template '$identifier' was added to the local store!</>");
            $io->newLine();

            return self::SUCCESS;
        }

        $io->error("Couldn't find template '$identifier'.");
        return self::FAILURE;
    }
}
