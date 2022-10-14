<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application
{
    public ConsoleApplication $app;

    public function __construct()
    {
        $this->app = new ConsoleApplication('MTRGen', '1.0.0');
        $this->app->addCommands([
            new GenerateCommand($this->app),
            new SaveTemplateCommand(),
            new RemoveTemplateCommand(),
            new PublishTemplateCommand(),
            new LoginCommand(),
            new AddTemplateCommand(),
            new CreateUserCommand(),
            new UseCommand(),
            new ListTemplatesCommand(),
            new SaveBundleCommand(),
            new ValidateCommand(),
        ]);
        $this->app->setDefaultCommand('generate');
    }
}
