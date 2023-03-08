<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Store\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class RemoveTemplateCommand extends Command
{
    protected static $defaultName = 'remove';
    protected static $defaultDescription = 'Removes a template from the global storage.';

    public function configure(): void
    {
        $this->setAliases(['rm']);
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the template to remove.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name') ?? null;

        $storage = new Storage;
        if ($storage->remove($name)) {
            $output->writeln("<fg=red>Template '$name' removed!</>");
            $io->newLine();

            return self::SUCCESS;
        }

        $io->error("Couldn't find template with name '$name'.");
        return self::FAILURE;
    }
}
