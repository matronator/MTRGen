<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Store\Storage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class RepairCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'repair';
    protected static $defaultDescription = 'Repairs the local store.';

    public function configure(): void
    {
        $this->setAliases(['r']);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $storage = new Storage;
        $storage->repairStore();

        $this->io->success('Local store repaired!');

        return self::SUCCESS;
    }
}
