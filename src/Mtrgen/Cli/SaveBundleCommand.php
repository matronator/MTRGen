<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\ClassicGenerator;
use Matronator\Parsem\Parser;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
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
class SaveBundleCommand extends Command
{
    protected static $defaultName = 'save-bundle';
    protected static $defaultDescription = 'Creates a bundle from two or more template files and add it to your local store.';

    public function configure(): void
    {
        $this->setAliases(['sb', 'save-b']);
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the bundle.');
        $this->addArgument('templates', InputArgument::IS_ARRAY, 'Paths to the template files separated by space.');
        $this->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'The file format that should be used for the bundle. (json, yaml or neon)');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name') ?? null;
        $templates = $input->getArgument('templates') ?? [];
        $format = $input->getOption('format') ?? 'json';

        if (!$templates || count($templates) < 2) {
            $io->error('You must include at least two templates.');
            return self::INVALID;
        }

        /** @var SymfonyQuestionHelper $helper */
        $helper = $this->getHelper('question');
        if (!$name) {
            $io->newLine();
            $nameQuestion = new Question('<comment><options=bold>Enter a name for your bundle</>:</comment> ');
            $validateName = Validation::createCallable(new Regex([
                'pattern' => '/^[A-Za-z0-9_-]+?$/',
                'message' => 'Name can only contain letters, numbers, underscore and a dash.',
            ]));
            $nameQuestion->setValidator($validateName);
            $name = $helper->ask($input, $output, $nameQuestion);
            $io->newLine();
        }

        if (!in_array($format, ['json', 'yaml', 'neon'])) {
            $io->text('<fg=yellow>Invalid or unsupported format.</>');
            $formatQuestion = new ChoiceQuestion('<comment><options=bold>Choose the output format for your bundle</>:</comment> ', ['json', 'yaml', 'neon'], 'json');
            $format = $helper->ask($input, $output, $formatQuestion);
            $io->newLine();
        }

        $data = (object) [
            'name' => $name,
            'templates' => [],
        ];

        $storage = new Storage;
        foreach ($templates as $key => $templatePath) {
            if (!Parser::isValid($templatePath)) {
                $io->error("Template '{$templatePath}' is not valid.");
                return self::INVALID;
            }

            $templateName = ClassicGenerator::getName($templatePath);
            if (!$storage->save($templatePath, null, $name)) {
                $io->error("File '{$templatePath}' doesn't exist.");
                return self::FAILURE;
            }

            $newPath = Storage::getBasename($templatePath);
            $data->templates[] = (object) [
                'name' => $templateName,
                'path' => $name . DIRECTORY_SEPARATOR . $newPath,
            ];
            $output->writeln("Template '$templateName' added to bundle!");
            if (array_key_last($templates) === $key) $io->newLine();
        }

        if ($storage->saveBundle($data, $format)) {
            $output->writeln("<fg=green>Bundle '$name' saved!</>");
            $io->newLine();
            return self::SUCCESS;
        }

        $io->error("File '$name' doesn't exists");
        return self::FAILURE;
    }
}
