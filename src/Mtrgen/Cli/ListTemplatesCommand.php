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
class ListTemplatesCommand extends Command
{
    protected static $defaultName = 'saved';
    protected static $defaultDescription = 'List all saved templates in the local store.';

    public function configure(): void
    {
        $this->setAliases(['ls']);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('<fg=green>Saved templates:</>');
        $io->newLine();

        $storage = new Storage;
        $io->listing(array_keys((array) $storage->listAll()));

        return self::SUCCESS;
    }
}
