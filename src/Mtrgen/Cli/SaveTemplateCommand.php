<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class SaveTemplateCommand extends Command
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
        $io = new SymfonyStyle($input, $output);

        $path = $input->getArgument('path') ?? null;
        $alias = $input->getOption('alias') ?? null;

        $helper = $this->getHelper('question');
        if (!$path) {
            $io->newLine();
            $pathQuestion = new Question('<comment><options=bold>Enter the path to your template</>:</comment> ');
            $validatePath = Validation::createCallable(new Regex([
                'pattern' => '/^(?![\/])(?![.+?\/]*[\/]$)[.+?\/]*/',
                'message' => 'Value must be a valid path without leading or trailing slashes.',
            ]));
            $pathQuestion->setValidator($validatePath);
            $path = $helper->ask($input, $output, $pathQuestion);
            $io->newLine();
        }

        $storage = new Storage;
        if ($storage->save($path, $alias)) {
            $name = $alias ?? Generator::getName($path);
            $output->writeln("<fg=green>Template '$name' added from '$path'!</>");
            $io->newLine();

            return self::SUCCESS;
        }

        $io->error("File '$path' doesn't exists");
        return self::FAILURE;
    }
}
