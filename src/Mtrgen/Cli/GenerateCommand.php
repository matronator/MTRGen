<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\FileGenerator;
use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\Generator;
use Matronator\Parsem\Parser;
use SplFileObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
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
class GenerateCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'generate';
    protected static $defaultDescription = 'Generates a file from template. The template can be specified by name if it\'s saved in the local store, or with a path to the template file.';

    public function configure(): void
    {
        $this->setAliases(['gen']);
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Optionally you can provide a path to a template file to generate from that file instead.');
        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of the template to generate under which it\'s saved in the local store.');
        $this->addArgument('args', InputArgument::IS_ARRAY, 'Arguments to pass to the template (\'key=value\' items seperated by space).');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $helper = $this->getHelper('question');

        $path = $input->getOption('path') ?? null;

        if (!$path) {
            $name = $input->getArgument('name') ?? null;

            if (!$name) {
                $path = $this->askName($helper);
            } else {
                $path = $this->storage->getFullPath($name);
                if (!$path) {
                    $this->io->text("<options=bold;fg=red>Template '$name' not found in the local store.</>");
                    return self::FAILURE;
                }
            }
        }

        if (!$path) {
            $path = $this->askPath($helper);
        }

        $name = Generator::getName($path);

        if (!$this->storage->isBundle($path)) {
            $arguments = $this->getArguments($input->getArgument('args')) ?? null;
            if (!$arguments) {
                $arguments = $this->askArguments($helper, $path);
            }

            $output->writeln("Generating file from template <options=bold>{$name}</>...");
            $this->io->newLine();
    
            FileGenerator::writeFile(Generator::parseFile($path, $arguments));
        } else {
            $output->writeln("Generating files from bundle <options=bold>{$name}</>...");
            $this->io->newLine();

            $bundle = Parser::decodeByExtension($path);
            $arguments = $this->getArguments($input->getArgument('args')) ?? null;
            if (!$arguments) {
                foreach ($bundle->templates as $temp) {
                    $templatePath = Path::canonicalize($this->storage->templateDir . DIRECTORY_SEPARATOR . $temp->path);
                    $arguments = $this->askArguments($helper, $templatePath);
                    $templateName = $temp->name;
                    $output->writeln("Generating file from template <options=bold>{$templateName}</>...");
                    FileGenerator::writeFile(Generator::parseFile($templatePath, $arguments));
                }
            } else {
                foreach ($bundle->templates as $temp) {
                    $templatePath = Path::canonicalize($this->storage->templateDir . DIRECTORY_SEPARATOR . $temp->path);
                    $templateName = $temp->name;
                    $output->writeln("Generating file from template <options=bold>{$templateName}</>...");
                    FileGenerator::writeFile(Generator::parseFile($templatePath, $arguments));
                }
            }
        }

        $output->writeln('<fg=green>Done!</>');
        $this->io->newLine();

        return self::SUCCESS;
    }
}
