<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Registry\Profile;
use Matronator\Mtrgen\Template\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class PublishTemplateCommand extends Command
{
    protected static $defaultName = 'publish';
    protected static $defaultDescription = 'Publish template to the online template repository.';

    public function configure(): void
    {
        $this->setAliases(['pub']);
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the template file.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getArgument('path') ?? null;

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

        $connection = new Connection;
        $response = $connection->postTemplate($path, $output);
        if (is_string($response)) {
            $io->text($response);
            $io->newLine();

            return self::FAILURE;
        }

        if (is_object($response)) {
            if ($response->status !== 'success') {
                $io->text('<fg=red>'.$response->status.'</>');
                $io->error($response->message);
                return self::FAILURE;
            }
            $name = Generator::getName($path);
            $profile = (new Profile)->loadProfile();
            $fullname = $profile->username . '/' . $name;
            $output->writeln("<fg=green>Template '$name' published as '$fullname'!</>");
            $io->newLine();

            return self::SUCCESS;
        }

        $io->error("File '$path' doesn't exists");
        return self::FAILURE;
    }
}
