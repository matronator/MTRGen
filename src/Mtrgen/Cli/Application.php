<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

class Application
{
    public MtrgenApplication $app;

    public function __construct()
    {
        $this->app = new MtrgenApplication('MTRGen', '2.0.0');
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
    }
}
