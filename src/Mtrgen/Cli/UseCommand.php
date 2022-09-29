<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\FileGenerator;
use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Template\Generator;
use Matronator\Parsem\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class UseCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'use';
    protected static $defaultDescription = 'Generates a file using a template from the online registry.';

    public function configure(): void
    {
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'The identifier of the template to use from the online registry.');
        $this->addArgument('args', InputArgument::IS_ARRAY, 'Arguments to pass to the template (\'key=value\' items seperated by space).');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        
        $helper = $this->getHelper('question');
        $identifier = $input->getArgument('identifier') ?? null;
        if (!$identifier) {
            $this->io->newLine();
            $nameQuestion = new Question('<comment><options=bold>Enter the template identifier (vendor/name)</>:</comment> ');
            $validateName = Validation::createCallable(new Regex([
                'pattern' => '/^[a-zA-Z0-9_-]+?\/[a-zA-Z0-9_-]+?$/',
                'message' => 'Name must be in format "vendor/name".',
            ]));
            $nameQuestion->setValidator($validateName);
            $identifier = $helper->ask($input, $output, $nameQuestion);
            $this->io->newLine();
        }

        $template = $this->connection->getTemplate($identifier);
        $arguments = $this->getArguments($input->getArgument('args')) ?? null;
        if (!$this->storage->isBundle($template->filename)) {
            if (!$arguments) {
                $contents = $template->contents;
                $arguments = $this->askArguments($helper, null, $identifier, $template);
            }
    
            $output->writeln("Generating file from template <options=bold>$identifier</>...");
            $this->io->newLine();
    
            FileGenerator::writeFile(Generator::parse($template->filename, $contents, $arguments));
        } else {
            $output->writeln("Generating files from bundle <options=bold>$identifier</>...");
            $bundle = Parser::decodeByExtension($template->filename, $template->contents);
            foreach ($bundle->templates as $temp) {
                $downloaded = $this->connection->getTemplateFromBundle($identifier, $temp->name);
                $templateName = $temp->name;
                $this->io->title("<fg=yellow>Template <options=bold>{$templateName}</>:</>");
                $arguments = $this->askArguments($helper, null, $identifier, $template, $downloaded->contents);
                $output->writeln("Generating file from template <options=bold>{$templateName}</>...");
                FileGenerator::writeFile(Generator::parse($temp->path, $downloaded->contents, $arguments));
                $this->io->newLine();
            }
        }

        $output->writeln('<fg=green>Done!</>');
        $this->io->newLine();

        return self::SUCCESS;
    }
}
