<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Tests\Template;

require __DIR__ . '/../bootstrap.php';

use Matronator\Mtrgen\Template\Generator;
use Tester\Assert;
use Tester\TestCase;

class GeneratorTest extends TestCase
{
    public const TEST_TEMPLATE = <<<EOT
--- MTRGEN ---
name: js-template
filename: <% name %>.js
path: assets/js
--- /MTRGEN ---

document.addEventListener('<% event %>', function() {
    var template = document.querySelector('#<% id="myId" %>');
    var templateContent = template.content;
    template.classList.add('<% classes="TEMPLATE"|lower %>');
    var clone = document.importNode(templateContent, true);
    document.body.appendChild(clone);
});
EOT;

    public const PARSED_TEMPLATE = <<<EOT
document.addEventListener('DOMContentLoaded', function() {
    var template = document.querySelector('#my-template');
    var templateContent = template.content;
    template.classList.add('template');
    var clone = document.importNode(templateContent, true);
    document.body.appendChild(clone);
});
EOT;

    public const PARSED_FILE = <<<EOT
<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Cli;

use Matronator\Parsem\Parser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends BaseGeneratorCommand
{
    protected static \$defaultName = 'hello';
    protected static \$defaultDescription = 'Hello world!';

    public function configure(): void
    {
        \$this->setAliases(['hi']);
    }

    public function execute(InputInterface \$input, OutputInterface \$output): int
    {
        parent::execute(\$input, \$output);

        \$this->io->newLine();
        return self::SUCCESS;
    }
}

EOT;

    public function testGetTemplateHeader()
    {
        $expected = static::TEST_TEMPLATE;

        $header = Generator::getTemplateHeader($expected);
        Assert::equal($header->name, 'js-template');
        Assert::equal($header->filename, '<% name %>.js');
        Assert::equal($header->path, 'assets/js');
    }

    public function testFileGeneration()
    {
        $expected = static::PARSED_TEMPLATE;

        Generator::writeFiles(Generator::parseAnyFile('../templates/js-template.js.mtr', [
            'name' => 'my-template',
            'event' => 'DOMContentLoaded',
            'id' => 'my-template',
        ]));

        Assert::matchFile('assets/js/my-template.js', $expected);
    }

    public function testPhpFromFile()
    {
        $expected = static::PARSED_FILE;

        Generator::writeFiles(Generator::parseAnyFile('../templates/Command.php.mtr', [
            'namespace' => '\Cli',
            'commandName' => 'hello',
            'commandAliases' => 'hi',
            'commandDescription' => 'Hello world!',
        ]));

        Assert::matchFile('src/Mtrgen/Cli/HelloCommand.php', $expected);
    }

    public function tearDown(): void
    {
        if (file_exists('assets/js/my-template.js')) {
            unlink('assets/js/my-template.js');
            rmdir('assets/js');
            rmdir('assets');
        }
        if (file_exists('src/Mtrgen/Cli/HelloCommand.php')) {
            unlink('src/Mtrgen/Cli/HelloCommand.php');
        }
    }
}

(new GeneratorTest)->run();
