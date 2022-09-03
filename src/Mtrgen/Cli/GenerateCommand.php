<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\FileGenerator;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\Generator;
use Matronator\Parsem\Parser;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\NullOutputFormatter;
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
class GenerateCommand extends Command
{
    private const CUSTOM_TEMPLATE_ANSWER = 'Custom template (enter path to the file)';

    protected static $defaultName = 'generate';
    protected static $defaultDescription = 'Generates a file from template. The template can be specified by name if it\'s saved in the global store, or with a path to the template file.';

    public function configure(): void
    {
        $this->setAliases(['gen']);
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Optionally you can provide a path to a template file to generate from that file instead.');
        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of the template to generate under which it\'s saved in the global store.');
        $this->addArgument('args', InputArgument::IS_ARRAY, 'Arguments to pass to the template (\'key=value\' items seperated by space).');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $storage = new Storage;
        $helper = $this->getHelper('question');

        $path = $input->getOption('path') ?? null;

        if (!$path) {
            $name = $input->getArgument('name') ?? null;

            if (!$name) {
                $path = $this->askName($helper, $io, $storage, $input, $output);
            } else {
                $path = $storage->getFullPath($name);
            }
        }

        $arguments = $this->getArguments($input->getArgument('args')) ?? null;

        if (!$path) {
            $io->newLine();
            $pathQuestion = new Question('<comment><options=bold>Enter the path to your template file</>:</comment> ');
            $validatePath = Validation::createCallable(new Regex([
                'pattern' => '/^(?![\/])(?![.+?\/]*[\/]$)[.+?\/]*/',
                'message' => 'Value must be a valid path without leading or trailing slashes.',
            ]));
            $pathQuestion->setValidator($validatePath);
            $path = $helper->ask($input, $output, $pathQuestion);
            $io->newLine();
        }

        if (!$arguments) {
            $template = $this->getTemplate($path, $io);
            if (!$template)
                return Command::FAILURE;

            $output->writeln('<fg=green>Template found!</>');
            $output->writeln('Looking for template parameters...');

            $args = Parser::getArguments($template);
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

        $name = Generator::getName($path);

        $output->writeln("Generating file from template <options=bold>{$name}</>...");
        $io->newLine();

        FileGenerator::writeFile(Generator::parseFile($path, $arguments));

        $output->writeln('<fg=green>Done!</>');
        $io->newLine();

        return self::SUCCESS;
    }

    private function askName(mixed $helper, SymfonyStyle $io, Storage $storage, InputInterface $input, OutputInterface $output): ?string
    {
        $templates = $storage->listAll();
        $choices = [];
        $choices[] = self::CUSTOM_TEMPLATE_ANSWER;
        foreach ($templates as $name => $path) {
            $choices[] = $name;
        }

        $io->newLine();
        $nameQuestion = new ChoiceQuestion('<comment><options=bold>Select the template to generate</>:</comment> ', $choices);
        $name = $helper->ask($input, $output, $nameQuestion);
        $io->newLine();

        if ($name !== self::CUSTOM_TEMPLATE_ANSWER) {
            $path = $storage->getFullPath($name);
            return $path;
        }

        return null;
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

    private function getTemplate(string $path, SymfonyStyle $io): ?string
    {
        if (!file_exists($path)) {
            $io->error("File '$path' doesn't exists.");
            return null;
        }
        $file = new SplFileObject($path);

        if (!in_array($file->getExtension(), ['yml', 'yaml', 'json', 'neon'])) {
            $io->error("File '$path' isn't of a valid type (supported extensions are: yml, yaml, json, neon).");
            return null;
        }

        return $file->fread($file->getSize());
    }
}
