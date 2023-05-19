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
// --- MTRGEN ---
// name: js-template
// filename: <% name %>.js
// path: assets/js
// --- MTRGEN ---

document.addEventListener('<% event %>', function() {
    var template = document.querySelector('#<% id="myId" %>');
    var templateContent = template.content;
    template.classList.add('<% classes="template"|lower %>');
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

        Generator::writeFiles(Generator::parseAnyFile('../templates/js-template.mtr.js', [
            'name' => 'my-template',
            'event' => 'DOMContentLoaded',
            'id' => 'my-template',
        ]));

        Assert::matchFile('assets/js/my-template.js', $expected);
    }

    public function tearDown(): void
    {
        if (file_exists('assets/js/my-template.js')) {
            unlink('assets/js/my-template.js');
            rmdir('assets/js');
            rmdir('assets');
        }
    }
}

(new GeneratorTest)->run();
