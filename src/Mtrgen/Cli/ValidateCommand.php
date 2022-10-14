<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\Generator;
use Matronator\Parsem\Parser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class ValidateCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'validate';
    protected static $defaultDescription = 'Check if a file is valid template or bundle.';

    public function configure(): void
    {
        $this->setAliases(['v']);
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the file to validate.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $path = $input->getArgument('path') ?? null;

        $helper = $this->getHelper('question');
        if (!$path) {
            $path = $this->askPath($helper);
        }

        if (!file_exists($path)) {
            $this->io->error("File '$path' doesn't exists");
            return self::FAILURE;
        }

        if ($this->storage->isBundle($path)) {
            if (Parser::isValidBundle($path)) {
                $this->io->text("<fg=green>File '$path' is a valid bundle.</>");
            } else {
                $this->io->text("<fg=red>File '$path' is not a valid bundle.</>");
            }
        } else {
            if (Parser::isValid($path)) {
                $this->io->text("<fg=green>File '$path' is a valid template.</>");
            } else {
                $this->io->text("<fg=red>File '$path' is not a valid template.</>");
            }
        }

        $this->io->newLine();
        return self::SUCCESS;
    }
}
