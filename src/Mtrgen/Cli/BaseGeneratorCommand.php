<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Parsem\Parser;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

abstract class BaseGeneratorCommand extends Command
{
    protected const CUSTOM_TEMPLATE_ANSWER = 'Custom template (enter path to the file)';

    public SymfonyStyle $io;
    public Storage $storage;
    public Connection $connection;
    public OutputInterface $output;
    public InputInterface $input;

    public function __construct()
    {
        $this->storage = new Storage;
        $this->connection = new Connection;

        parent::__construct();
    }

    public function execute(InputInterface $inputInterface, OutputInterface $outputInterface)
    {
        $this->input = $inputInterface;
        $this->output = $outputInterface;
        $this->io = new SymfonyStyle($this->input, $this->output);
    }

    protected function askName($helper): ?string
    {
        $templates = $this->storage->listAll();
        $choices = [];
        $choices[] = self::CUSTOM_TEMPLATE_ANSWER;
        foreach ($templates as $name => $path) {
            $choices[] = $name;
        }

        $this->io->newLine();
        $nameQuestion = new ChoiceQuestion('<comment><options=bold>Select the template to generate</>:</comment> ', $choices);
        $name = $helper->ask($this->input, $this->output, $nameQuestion);
        $this->io->newLine();

        if ($name !== self::CUSTOM_TEMPLATE_ANSWER) {
            $path = $this->storage->getFullPath($name);
            return $path;
        }

        return null;
    }

    protected function askPath($helper): string
    {
        $this->io->newLine();
        $pathQuestion = new Question('<comment><options=bold>Enter the path to your template file</>:</comment> ');
        $validatePath = Validation::createCallable(new Regex([
            'pattern' => '/^(?![\/])(?![.+?\/]*[\/]$)[.+?\/]*/',
            'message' => 'Value must be a valid path without leading or trailing slashes.',
        ]));
        $pathQuestion->setValidator($validatePath);
        $path = $helper->ask($this->input, $this->output, $pathQuestion);
        $this->io->newLine();

        return $path;
    }

    protected function getArguments(array $args): array
    {
        $arguments = [];
        foreach ($args as $arg) {
            $exploded = explode('=', $arg);
            $arguments[$exploded[0]] = $exploded[1];
        }

        return $arguments;
    }

    protected function getTemplate(string $path): ?string
    {
        if (!file_exists($path)) {
            $this->io->error("File '$path' doesn't exists.");
            return null;
        }
        $file = new SplFileObject($path);

        if (!in_array($file->getExtension(), ['yml', 'yaml', 'json', 'neon'])) {
            $this->io->error("File '$path' isn't of a valid type (supported extensions are: yml, yaml, json, neon).");
            return null;
        }

        return $file->fread($file->getSize());
    }

    protected function askArguments($helper, ?string $path = null, ?string $identifier = null, $template = null, ?string $contents = null): array
    {
        if ($identifier) {
            if (!$template) $template = $this->connection->getTemplate($identifier);
            if (!$contents) $contents = $template->contents;
            if (!$template || !$contents) {
                $this->io->error('Template not found.');
                return Command::FAILURE;
            }
        } else {
            $template = $this->getTemplate($path, $this->io);
            if (!$template)
                return Command::FAILURE;
        }
        
        $this->output->writeln('<fg=green>Template found!</>');
        $this->output->writeln('Looking for template parameters...');

        $args = Parser::getArguments($identifier ? $contents : $template);
        if ($args !== []) {
            $this->io->writeln('<fg=green>Template parameters found!</>');
            $this->io->newLine();
            $arguments = [];
            foreach ($args as $arg) {
                $argQuestion = new Question("<comment><options=bold>Enter the value for parameter '$arg'</>:</comment> ");
                $arguments[$arg] = $helper->ask($this->input, $this->output, $argQuestion);
                $this->io->newLine();
            }
            return $arguments;
        }

        return [];
    }
}
