<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application
{
    public ConsoleApplication $app;

    public function __construct()
    {
        $this->app = new ConsoleApplication('Mtrgen', '1.4.1');
        $this->app->addCommands([
            new GenerateCommand(),
            new SaveTemplateCommand(),
            new RemoveTemplateCommand(),
        ]);
        $this->app->setDefaultCommand('generate');
    }
}
