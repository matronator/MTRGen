<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\ClassicGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class SaveTemplateCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'save';
    protected static $defaultDescription = 'Saves a template to the global store.';

    public function configure(): void
    {
        $this->setAliases(['s']);
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the template file.');
        $this->addOption('alias', 'a', InputOption::VALUE_REQUIRED, 'Alias to use instead of the name defined inside the template.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $path = $input->getArgument('path') ?? null;
        $alias = $input->getOption('alias') ?? null;

        $helper = $this->getHelper('question');
        if (!$path) {
            $path = $this->askPath($helper);
        }

        $storage = new Storage;
        if ($storage->save($path, $alias)) {
            $name = $alias ?? ClassicGenerator::getName($path);
            $output->writeln("<fg=green>Template '$name' added from '$path'!</>");
            $this->io->newLine();

            return self::SUCCESS;
        }

        $this->io->error("File '$path' doesn't exists");
        return self::FAILURE;
    }
}
