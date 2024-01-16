<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\ClassicFileGenerator;
use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Template;
use Matronator\Mtrgen\Template\ClassicGenerator;
use Matronator\Mtrgen\Template\Generator;
use Matronator\Mtrgen\Template\TemplateNotFoundException;
use Matronator\Parsem\Parser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class GenerateCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'generate';
    protected static $defaultDescription = 'Generates a file from template. The template can be specified by name if it\'s saved in the local store, or with a path to the template file.';

    public function configure(): void
    {
        $this->setAliases(['gen']);
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Optionally you can provide a path to a template file to generate from that file instead.');
        $this->addOption('comment-syntax', 'cs', InputOption::VALUE_NONE, 'If set, the comment syntax will be used to generate the file (if the template supports it).');
        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of the template to generate under which it\'s saved in the local store.');
        $this->addArgument('args', InputArgument::IS_ARRAY|InputArgument::OPTIONAL, 'Arguments to pass to the template (\'key=value\' items separated by space).');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $helper = $this->getHelper('question');

        $path = $input->getOption('path');

        if (!$path) {
            $name = $input->getArgument('name');

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
        
        try {
            $isLegacy = Template::isLegacy($path);
        } catch (TemplateNotFoundException $e) {
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
        $name = $isLegacy ? ClassicGenerator::getName($path) : Generator::getName($this->getTemplate($path));

        if ($isLegacy) {
            if (!$this->storage->isBundle($path)) {
                $arguments = $this->getArguments($input->getArgument('args'));
                if (!$arguments) {
                    $arguments = $this->askArguments($helper, $path);
                }
    
                $output->writeln("Generating file from template <options=bold>{$name}</>...");
                $this->io->newLine();
    
                ClassicFileGenerator::writeFile(ClassicGenerator::parseFile($path, $arguments));
            } else {
                $output->writeln("Generating files from bundle <options=bold>{$name}</>...");
                $this->io->newLine();
    
                $bundle = Parser::decodeByExtension($path);
                $arguments = $this->getArguments($input->getArgument('args'));
                if (!$arguments) {
                    foreach ($bundle->templates as $temp) {
                        $templatePath = Path::canonicalize($this->storage->templateDir . DIRECTORY_SEPARATOR . $temp->path);
                        $arguments = $this->askArguments($helper, $templatePath);
                        $templateName = $temp->name;
                        $output->writeln("Generating file from template <options=bold>{$templateName}</>...");
                        ClassicFileGenerator::writeFile(ClassicGenerator::parseFile($templatePath, $arguments));
                    }
                } else {
                    foreach ($bundle->templates as $temp) {
                        $templatePath = Path::canonicalize($this->storage->templateDir . DIRECTORY_SEPARATOR . $temp->path);
                        $templateName = $temp->name;
                        $output->writeln("Generating file from template <options=bold>{$templateName}</>...");
                        ClassicFileGenerator::writeFile(ClassicGenerator::parseFile($templatePath, $arguments));
                    }
                }
            }
        } else {
            $arguments = $this->getArguments($input->getArgument('args'));
            $useDefaults = true;
            if (!$arguments) {
                $needsArguments = Parser::needsArguments($this->getTemplate($path));
                if ($needsArguments) {
                    $useDefaults = false;
                } else {
                    $askArguments = $this->shouldAskArguments($helper);
                    if ($askArguments) {
                        $useDefaults = false;
                    }
                }
            }
            if ($useDefaults) {
                $arguments = Parser::getArguments($this->getTemplate($path), $input->getOption('comment-syntax') ? Generator::COMMENT_PATTERN : null)->defaults;
            } else {
                $arguments = $this->askArguments($helper, $path);
            }

            $file = Generator::parseAnyFile($path, $arguments, $input->getOption('comment-syntax'));

            $output->writeln("Generating file '{$file->filename}' to '{$file->directory}' from template <options=bold>{$name}</>...");
            $this->io->newLine();

            Generator::writeFiles($file);
        }

        $output->writeln('<fg=green>Done!</>');
        $this->io->newLine();

        return self::SUCCESS;
    }
}
