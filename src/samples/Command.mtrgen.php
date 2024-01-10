// --- MTRGen ---
// name: command
// filename: <% commandName|upperFist %>Command.php
// path: src/Mtrgen/cli
// --- MTRGen ---
<?php

declare(strict_types=1);

namespace Matronator\Mtrgen<% namespace %>;

use Matronator\Parsem\Parser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// #[AsCommand('validate', 'Check if a file is valid template or bundle.', ['v'])]
class <% commandName|upperFirst %>Command extends BaseGeneratorCommand
{
    protected static $defaultName = '<% commandName|lower %>';
    protected static $defaultDescription = '<% commandDescription %>';

    public function configure(): void
    {
        $this->setAliases(['<% commandAliases %>']);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $this->io->newLine();
        return self::SUCCESS;
    }
}
