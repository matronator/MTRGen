<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Mtrgen\Registry\Connection;
use Matronator\Mtrgen\Registry\Profile;
use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Mtrgen\Template\ClassicGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// #[AsCommand('generate:entity', 'Generates an Entity file', ['gen:entity'])]
class PublishTemplateCommand extends BaseGeneratorCommand
{
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
        parent::execute($input, $output);

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
                return $this->checkResponse($response, $output, "<fg=green>Template '$name' published as '$fullname'!</>");
            }
        }
        if (!$path) {
            $path = $this->askName($helper);
        }
        if (!$path) {
            $path = $this->askPath($helper);
        }

        $response = $this->connection->postTemplate($path, $output);
        $name = ClassicGenerator::getName($path);
        $profile = (new Profile)->loadProfile();
        $fullname = strtolower($profile->username . '/' . $name);
        return $this->checkResponse($response, $output, "<fg=green>Template '$name' published as '$fullname'!</>");
    }

    /**
     * @param mixed $response
     * @param OutputInterface $output
     * @param string $name
     * @return integer
     */
    private function checkResponse(mixed $response, OutputInterface $output, string $message): int
    {
        if (is_string($response)) {
            $this->io->text($response);
            $this->io->newLine();

            return self::FAILURE;
        }

        if (is_object($response)) {
            if ($response->status !== 'success') {
                $this->io->text('<fg=red>'.$response->status.'</>');
                $this->io->error($response->message);
                return self::FAILURE;
            }
            $output->writeln($message);
            $this->io->newLine();

            return self::SUCCESS;
        }

        $this->io->error($response);
        return self::INVALID;
    }
}
