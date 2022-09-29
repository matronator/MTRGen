<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Registry\Profile;
use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\Generator;
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
class PublishTemplateCommand extends Command
{
    private const CUSTOM_TEMPLATE_ANSWER = 'Custom template (enter path to the file)';

    protected static $defaultName = 'publish';
    protected static $defaultDescription = 'Publish template to the online template repository.';

    public Storage $storage;
    public Connection $connection;

    public function __construct()
    {
        $this->storage = new Storage;
        $this->connection = new Connection;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setAliases(['pub']);
        $this->addArgument('name', InputArgument::OPTIONAL, 'Name of the template from the local store.');
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path to the template file.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name') ?? null;
        $path = $input->getOption('path') ?? null;

        $helper = $this->getHelper('question');
        if ($name) {
            $template = $this->storage->load($name);
            if ($template) {
                $path = $template->filename;
    
                $response = $this->connection->postTemplate(Path::canonicalize($this->storage->templateDir . DIRECTORY_SEPARATOR . $path), $output);
                $profile = (new Profile)->loadProfile();
                $fullname = strtolower($profile->username . '/' . $name);
                return $this->checkResponse($io, $response, $output, "<fg=green>Template '$name' published as '$fullname'!</>");
            }
        }
        if (!$path) {
            $path = $this->askName($helper, $io, $input, $output);
        }
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

        $response = $this->connection->postTemplate($path, $output);
        $name = Generator::getName($path);
        $profile = (new Profile)->loadProfile();
        $fullname = strtolower($profile->username . '/' . $name);
        return $this->checkResponse($io, $response, $output, "<fg=green>Template '$name' published as '$fullname'!</>");
    }

    /**
     * @param SymfonyStyle $io
     * @param mixed $response
     * @return integer
     */
    private function checkResponse(SymfonyStyle $io, mixed $response, OutputInterface $output, string $message): int
    {
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
            $output->writeln($message);
            $io->newLine();

            return self::SUCCESS;
        }

        $io->error($response);
        return self::INVALID;
    }

    private function askName(mixed $helper, SymfonyStyle $io, InputInterface $input, OutputInterface $output): ?string
    {
        $templates = $this->storage->listAll();
        $choices = [];
        $choices[] = self::CUSTOM_TEMPLATE_ANSWER;
        foreach ($templates as $name => $path) {
            $choices[] = $name;
        }

        $io->newLine();
        $nameQuestion = new ChoiceQuestion('<comment><options=bold>Select the template to generate</>:</comment> ', $choices);
        $name = $helper->ask($input, $output, $nameQuestion);

        if ($name !== self::CUSTOM_TEMPLATE_ANSWER) {
            $path = $this->storage->getFullPath($name);
            return $path;
        }

        return null;
    }
}
