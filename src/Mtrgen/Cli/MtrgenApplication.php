<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Symfony\Component\Console\Application;

class MtrgenApplication extends Application
{
    
    private static $logo = <<<LOGO
     __  ____________  _____       
    /  |/  /_  __/ _ \/ ___/__ ___ 
   / /|_/ / / / / , _/ (_ / -_) _ \
  /_/  /_/ /_/ /_/|_|\___/\__/_//_/


LOGO;

    private static $name = 'MTRGen';

    public function __construct(?string $name, $version = 'UNKNOWN')
    {
        if ($name !== null) {
            static::$name = $name;
        }

        $this->setName(static::$name);
        $this->setVersion($version);

        parent::__construct($name, $version);
    }

    // public function getHelp(): string
    // {
    //     return static::$logo . parent::getHelp();
    // }

    public function getVersion(): string
    {
        return 'version ' . parent::getVersion();
    }

    public function getLongVersion()
    {
        return '<info>' . static::$logo . '</info>' . parent::getLongVersion();
    }
}
