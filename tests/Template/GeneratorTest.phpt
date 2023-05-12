<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Matronator\Mtrgen\Cli\Application;
use Matronator\Mtrgen\Cli\GenerateCommand;
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
        var template = document.querySelector('#<% id %>');
        var templateContent = template.content;
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
        $expected = static::TEST_TEMPLATE;

        Assert::matchFile($expected, file_get_contents('assets/js/my-template.js'), 'File generation');
    }
}

(new GeneratorTest)->run();
